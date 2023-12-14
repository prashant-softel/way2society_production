
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

</head>
</html>

<?php


	class read_master_daybook
	{
	
	
	function __construct()
	{
	}
	
	function readDataBook($xmlfile)
	{
		$xmlfileData = $xmlfile;
		$CategoryArray=array();
		$LedgerArray=array();
		$table="";
		
		$table = '<table name=test
							style = "margin:0 auto;
                            width:95%;
                            margin: 6px;
                            overflow:auto;
                            font-family: helvetica,arial,sans-serif;
                            font-size:14px;
                            color:#333333;
                            border-width: 1px;
                            border-color: #666666;
                            border-collapse: collapse;
                            text-align: center;">
            <thead>
            
            <tr>
           
            <td style="border-width: 1px; padding: 8px; border-style: solid; vertical-align: center;border-color:#666666;">    		<b>Parent</b></td>
            <td style="border-width: 1px;padding: 8px;border-style: solid;vertical-align: center;border-color: #666666;">	
			<b>Category</b></td>							
            <td style="border-width: 1px;padding: 8px;border-style: solid;vertical-align: center;border-color: #666666;">
			<b>Ledger</b></td>
           
			</tr>
			</thead>
			<tbody>
			<tr>';
			
				$XMLCategoryArray=array();
				$XMLLedgerArray=array();
				
				foreach($xmlfileData->BODY->IMPORTDATA->REQUESTDATA->TALLYMESSAGE as $Body)
				{
					foreach($Body->GROUP as $group)
					{
						$categoryArray=array();
						$Category=array();
						//$XMLArray=array();
						if($group['NAME']!='')
						{
							if($group->PARENT == '')
							{
								$table.='<td style="border-width: 1px; padding: 8px; border-style: solid; vertical-align: center;border-color:#666666;">-</td>';
							}
							else
							{
								$table.='<td style="border-width: 1px; padding: 8px; border-style: solid; vertical-align: center;border-color:#666666;">'.$group->PARENT.'</td>';
							}
							$table.='<td style="border-width: 1px; padding: 8px; border-style: solid; vertical-align: center;border-color:#666666;">'.$group['NAME'].'
							</td> <td style="border-width: 1px; padding: 8px; border-style: solid; vertical-align: center;border-color:#666666;">-</td>';
							
							$categoryArray['Name'] = json_decode(json_encode($group['NAME'],true));
							$categoryArray['Parent'] =  json_decode(json_encode($group->PARENT,true));
							
						}
						foreach($categoryArray['Name'] as $arr)
						{
							$Category['Category']=$arr;
						}
						//var_dump($category['Parent']);
						foreach($categoryArray['Parent'] as $arr)
						{
							//var_dump($arr);
							$Category['Parent'] = $arr;
						}
						$XMLCategoryArray[]=$Category;
					}
					foreach($Body->LEDGER as $ledger)
					{
						
						
						$ledgerArray=array();
						$Ledger=array();
						
						if($ledger['NAME'] != '')
						{
							$table.='<td style="border-width: 1px; padding: 8px; border-style: solid; vertical-align: center;border-color:#666666;">'.$ledger->PARENT.'</td>
							<td style="border-width: 1px; padding: 8px; border-style: solid; vertical-align: center;border-color:#666666;">-</td>
							<td style="border-width: 1px; padding: 8px; border-style: solid; vertical-align: center;border-color:#666666;">'.$ledger['NAME'].'
							</td>';
							
							$ledgerArray['Name'] = json_decode(json_encode($ledger['NAME'],true));
							$ledgerArray['Parent'] =  json_decode(json_encode($ledger->PARENT,true));
							$ledgerArray['OpeningBalance'] =  json_decode(json_encode($ledger->OPENINGBALANCE,true));
						}
						foreach($ledgerArray['Name'] as $arr)
						{
							$Ledger['LedgerName']=$arr;
						}
						//var_dump($category['Parent']);
						foreach($ledgerArray['Parent'] as $arr)
						{
							//var_dump($arr);
							$Ledger['Parent'] = $arr;
						}
						foreach($ledgerArray['OpeningBalance'] as $arr)
						{
							//var_dump($arr);
							$Ledger['OpeningBalance'] = $arr;
						}
						$XMLLedgerArray[]=$Ledger;
					}
				
					$table.='</tr>';
				}	
				
				$CategoryArray=json_encode($XMLCategoryArray);
				$_SESSION['Category']=$CategoryArray;
				$LedgerArray=json_encode($XMLLedgerArray);
				$_SESSION['Ledger']=$LedgerArray;
				
		$table.='</table>';
			return $table;
	}//function
		
	}
	
	
	?>
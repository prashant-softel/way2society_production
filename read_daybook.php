
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>
.rows
{
border-width: 1px;
padding: 8px;
border-style: solid;
vertical-align: center;
border-color: #666666;	
}
</style>
</head>
</html>

<?php


	class read_daybook
	{
	
	
	function __construct()
	{
	}
	
	function readDataBook($xmlfile)
	{
	
		$xmlfileData = $xmlfile;
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
           
            <td style="border-width: 1px; padding: 8px; border-style: solid; vertical-align: center; border-color:#666666;">    		<b>Date</b></td>
            <td style="border-width: 1px;padding: 8px;border-style: solid;vertical-align: center;border-color: #666666;">	
			<b>GUID</b></td>							
            <td style="border-width: 1px;padding: 8px;border-style: solid;vertical-align: center;border-color: #666666;">
			<b>Description</b></td>
            <td style="border-width: 1px;padding: 8px;border-style: solid;vertical-align: center;border-color: #666666;">
			<b>VoucherType</b></td>
            <td style="border-width: 1px;padding: 8px;border-style: solid;vertical-align: center;border-color: #666666;">
			<b>VoucherNumber</b></td>';
			
			foreach($xmlfileData->BODY->IMPORTDATA->REQUESTDATA->TALLYMESSAGE as $Body)
			{
				//echo "In For Each";
				foreach($Body->VOUCHER as $voucher)
				{
					$new = "ALLLEDGERENTRIES.LIST";
					$maxCount[] = count($voucher->$new);
					
					
				}
			}
			for($i=1;$i<=max($maxCount);$i++)
			{
				
					$ledgers .= '<td style="border-width: 1px; padding: 8px; border-style: solid; vertical-align: center; border-color:#666666;"><b> Ledger Name </b></td>
            		<td style="border-width: 1px; padding: 8px; border-style: solid; vertical-align: center; border-color:#666666;"><b> Ledger Amount </b></td>';
                
			}
			$table.=$ledgers;
			$table.='</tr>
            </thead>';
            
			$finalArray=array();
			$xmlArray=array();
			$toData=array();
			$byData=array();
			
			foreach($xmlfileData->BODY->IMPORTDATA->REQUESTDATA->TALLYMESSAGE as $Body)
			{
				$cnt++; 
				$xmlData=array();
				foreach($Body->VOUCHER as $voucher)
				{
					 $table.= '<tbody style="border:thick">
                    
					<tr>
      				<td style="border-width: 1px; padding: 8px; border-style: solid; vertical-align: center; border-color:#666666;">'.$this->ConvertIntoDateFormat($voucher->DATE).'</td>
                    <td style="border-width: 1px; padding: 8px; border-style: solid; vertical-align: center; border-color:#666666;">'.$voucher->GUID.'</td>
                    <td style="border-width: 1px; padding: 8px; border-style: solid; vertical-align: center; border-color:#666666;">'.$voucher->NARRATION.'</td>
                    <td style="border-width: 1px; padding: 8px; border-style: solid; vertical-align: center; border-color:#666666;">'.$voucher->VOUCHERTYPENAME.'</td>
                    <td style="border-width: 1px; padding: 8px; border-style: solid; vertical-align: center; border-color:#666666;">'.$voucher->VOUCHERNUMBER.'</td>';
					
					$xmlDate=$this->ConvertIntoDateFormat($voucher->DATE);
					$xmlVoucherType=$voucher->VOUCHERTYPENAME;
					$xmlVoucherNumber=$voucher->VOUCHERNUMBER;
					$xmlDescription=$voucher->NARRATION;
					$xmlGUID=$voucher->GUID;
					
					
					$new = "ALLLEDGERENTRIES.LIST";
					$maxCount[] = count($voucher->$new);
					$newCnt = 0;
					$byCnt=0;
					$toCnt=0;		
					$byLedger=array();
					$toLedger=array();
					$byLedgerAmount=array();
					foreach($voucher->$new as $ledger)
					{
						if($ledger->AMOUNT>0)
						{
							$table.='<td style="border-width: 1px; padding: 8px; border-style: solid; vertical-align: center; border-color:#666666;">'.$ledger->LEDGERNAME.'</td>
							<td style="border-width: 1px; padding: 8px; border-style: solid; vertical-align: center; border-color:#666666;">'.$ledger->AMOUNT.'</td>';
							
							$toCnt++;
								//echo "<br>By New Count :".$byCnt;
								$toLedger[$toCnt]['LedgerName']=json_decode(json_encode($ledger->LEDGERNAME), true);
								$toLedger[$toCnt]['Amount']=json_decode(json_encode($ledger->AMOUNT), true);
									
								
						}
						else
						{
							$table.='<td style="border-width: 1px; padding: 8px; border-style: solid; vertical-align: center; border-color:#666666;">'.$ledger->LEDGERNAME.'</td>
							<td style="border-width: 1px; padding: 8px; border-style: solid; vertical-align: center; border-color:#666666;">'.$ledger->AMOUNT.'</td>';
							$byCnt++;
						$byLedger[$byCnt]['LedgerName']=json_decode(json_encode($ledger->LEDGERNAME), true);
						$byLedger[$byCnt]['Amount']=json_decode(json_encode($ledger->AMOUNT), true);
						}
						
						
						$newCnt++;
					}
						
					$nextloopsize = max($maxCount) - $newCnt;
					if($nextloopsize > 0)
					{ 
						for($j = 0 ; $j < $nextloopsize ; $j++)
						{
							$table.='<td style="border-width: 1px; padding: 8px; border-style: solid; vertical-align: center; border-color:#666666;"></td>
							<td style="border-width: 1px; padding: 8px; border-style: solid; vertical-align: center; border-color:#666666;"></td>';
						}
						
					}
					$table.='</tr>';
					$xmlF=array();
					
					$Datearray = json_decode(json_encode($xmlDate), true);
					$VoucherTypeArray=json_decode(json_encode($xmlVoucherType), true);
					$VoucherNumberArray=json_decode(json_encode($xmlVoucherNumber), true);
					$GUIDArray=json_decode(json_encode($xmlGUID), true);
					$DescriptionArray=json_decode(json_encode($xmlDescription), true);
						
					$xmlF['Date']=$Datearray;	
					
					foreach($GUIDArray as $arr)
					{
						$xmlF['GUID']=$arr;	
					}
					
					foreach($VoucherTypeArray as $arr)
					{
						$xmlF['VoucherType']=$arr;
					}
					foreach($VoucherNumberArray as $arr)
					{
						$xmlF['VoucherNumber']=$arr;
					}
					foreach($DescriptionArray as $arr)
					{
						$xmlF['Description']=$arr;	
					}
					for($i = 1; $i <= sizeof($byLedger); $i++)
					{
						$xmlF['ByLedgers'][$byLedger[$i]['LedgerName'][0]] += abs($byLedger[$i]['Amount'][0]) ;
					}
					
					for($i = 1; $i <= sizeof($toLedger); $i++)
					{
						$xmlF['ToLedgers'][$toLedger[$i]['LedgerName'][0]] += abs($toLedger[$i]['Amount'][0]) ;
					}
					
					$finalArray[] = $xmlF;
					
					
				}
				
			}
			
			$final=json_encode($finalArray);
			$_SESSION['DayBook']=$final;
			
			$table.='</tbody></table>';
			
				
			return $table;
			
			
	}//function
	
	 public function ConvertIntoDateFormat($DateString)
  	{
		$Year = substr($DateString,0,4);
	  	$Month = substr($DateString,4,2);
	  	$Date = substr($DateString,6,2);
	  	return $FormatedDate = $Year.'-'.$Month.'-'.$Date;
  	}
		
	}
	
	
	?>
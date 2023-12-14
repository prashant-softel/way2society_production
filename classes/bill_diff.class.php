<?php 

if(!isset($_SESSION)){ session_start(); }

class Bill_Diff 
{
	
	public $m_dbConnRoot;
	public $m_dbConn;
	
	function __construct($dbConn, $dbConnRoot = "")
	{
		$this->m_dbConnRoot = $dbConnRoot;
		$this->m_dbConn = $dbConn;
	}




	public function combobox($query, $id, $defaultText = '')
	{
		if($defaultText <> '')
		{
			$str = '<option value="">' . $defaultText . '</option>';
		}
		
		$data = $this->m_dbConn->select($query);
		if(!is_null($data))
		{
			$vowels = array('/', '-', '.', '*', '%', '&', ',', '"');
			foreach($data as $key => $value)
			{
				$i=0;
				foreach($value as $k => $v)
				{
					if($i==0)
					{
						
						$str.="<OPTION VALUE=".$v.' '.$sel.">";
					}
					else
					{
						$str.= str_replace($vowels, ' ', $v)."</OPTION>";
					}
				
					$i++;
				}
			}
		}
		return $str;
	}
	
	
public function compare_bill($data_array1, $data_array2,$addition_request)
	{
		

		$year_id_1=$data_array1['year_id_1'];
		$period_id_1=$data_array1['period_id_1'];
		$bill_type_1=$data_array1['bill_type_1'];


        $year_id_2=$data_array2['year_id_2'];
		$period_id_2=$data_array2['period_id_2'];
		$bill_type_2=$data_array2['bill_type_2'];

		$skip_interest=$addition_request['skip_interest'];
		$skip_gst=$addition_request['skip_gst'];
		$only_diff=$addition_request['only_diff'];

		$SkipLedgerArr=array();
	    if($skip_interest==1)
	    {
	        array_push($SkipLedgerArr,INTEREST_ON_PRINCIPLE_DUE);
	    }
	    if($skip_gst==1)
	    {
	        array_push($SkipLedgerArr,IGST_SERVICE_TAX);
	        array_push($SkipLedgerArr,CGST_SERVICE_TAX);
	        array_push($SkipLedgerArr,SGST_SERVICE_TAX);
	        array_push($SkipLedgerArr,CESS_SERVICE_TAX);
	    }

       if(count($SkipLedgerArr)>0)
       {
         $SkipLedger=implode(",",$SkipLedgerArr);
       }
       

		$qry1="SELECT billdetails.ID,billdetails.UnitID,billdetails.BillNumber,billregister.BillDate FROM `billdetails` JOIN `billregister` ON billdetails.BillRegisterID=billregister.ID  where billdetails.PeriodID='".$period_id_1."' and billdetails.BillType='".$bill_type_1."' ";
		$result1 = $this->m_dbConn->select($qry1);
		if(count($result1)>0)
		{
		
		foreach($result1 as $bill_detail)
		{
          $BillRefNo=$bill_detail['ID'];
          $BillUnitID=$bill_detail['UnitID'];
          $BillNumber=$bill_detail['BillNumber'];
          $BillDate=$bill_detail['BillDate'];
		
          $qry3="SELECT billdetails.ID,billdetails.UnitID,billdetails.BillNumber,billregister.BillDate FROM `billdetails` JOIN `billregister` ON billdetails.BillRegisterID=billregister.ID  where billdetails.PeriodID='".$period_id_2."' and billdetails.BillType='".$bill_type_2."' and billdetails.UnitID='".$BillUnitID."' ";
		  $result3 = $this->m_dbConn->select($qry3);
		  if(count($result3)>0)
		  {
		   $BillRefNo2=$result3[0]['ID'];
		   $BillUnitID2=$result3[0]['UnitID'];
		   $BillNumber2=$result3[0]['BillNumber'];
		   $BillDate2=$result3[0]['BillDate'];

		    $first_compare=$this->get_voucher_detail($BillRefNo,$SkipLedger);

		    $second_compare=$this->get_voucher_detail($BillRefNo2,$SkipLedger);


       if(count($first_compare)>0 and count($second_compare)>0){

		    if($first_compare!=$second_compare)
			  {
          
          if($only_diff==1)
          {
          $Diff_A=array_diff_assoc($first_compare, $second_compare);		  	
          $Diff_B=array_diff_assoc($second_compare, $first_compare);
          $A = implode(",", array_keys($Diff_A));
          $B = implode(",", array_keys($Diff_B));

          $compare_A=$this->get_ladger_detail($BillRefNo,$A);
		      $compare_B=$this->get_ladger_detail($BillRefNo2,$B);

          }
          else
          {
          $A = implode(",", array_keys($first_compare));
          $B = implode(",", array_keys($second_compare));

          $compare_A=$this->get_ladger_detail($BillRefNo,$A);
		      $compare_B=$this->get_ladger_detail($BillRefNo2,$B);
		      	
          }


         
			  	$data[]=array(
			  		          'UnitID_A'=>$BillUnitID,'BillRefNo_A'=>$BillRefNo,
			  		          'BillNumber_A'=>$BillNumber,'BillDate_A'=>$BillDate,'Voucher_Details_A'=>$compare_A,
			  		          'UnitID_B'=>$BillUnitID2,'BillRefNo_B'=>$BillRefNo2,
			  		          'BillNumber_B'=>$BillNumber2,'Voucher_Details_B'=>$compare_B,'BillDate_B'=>$BillDate2
			  		         );

			  }
			}
		  }
		  else
		  {
		  	//echo "no bill for this unit on select period";
		  }

		}
	  }
	  else
	  {
	  	//echo "no record for ";
	  }

	  return $data;

	}



	public function get_voucher_detail($BillRefNo,$SkipLedger)
	{
       
       if(!empty($SkipLedger))
       {
         $where=" and voucher_table.To NOT IN($SkipLedger)";
       }
       else
       {
         $where=" ";

       }

       $qry2="SELECT voucher_table.To, voucher_table.Credit,ledger_table.ledger_name,ledger_table.taxable,ledger_table.taxable_no_threshold FROM `voucher` as `voucher_table` join `ledger` as `ledger_table` on voucher_table.To = ledger_table.id WHERE voucher_table.RefNo='".$BillRefNo."' and 
		     voucher_table.RefTableID =".TABLE_BILLREGISTER." ".$where." ORDER BY `ledger_table`.taxable ASC, voucher_table.id ASC";
	   $result2 = $this->m_dbConn->select($qry2);
	   $data=[];
	   foreach($result2 as $result)
	   {
     array_push($data[$result['To']]);
     $data[$result['To']]=$result['Credit'];
	   }

	   return $data;
	}



	public function get_ladger_detail($BillRefNo,$Ledger)
	{
       
      if(!empty($Ledger))
       {
          
          $qry2="SELECT voucher_table.To, voucher_table.Credit,ledger_table.ledger_name,ledger_table.taxable,ledger_table.taxable_no_threshold FROM `voucher` as `voucher_table` join `ledger` as `ledger_table` on voucher_table.To = ledger_table.id WHERE voucher_table.RefTableID =".TABLE_BILLREGISTER."  and voucher_table.RefNo='".$BillRefNo."' and voucher_table.To IN($Ledger) ORDER BY `ledger_table`.taxable ASC, voucher_table.id ASC";
	       
	         $result2 = $this->m_dbConn->select($qry2);
	    }
       
	   return $result2;
	}
	

}
?>
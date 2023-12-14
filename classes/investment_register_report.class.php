<?php
include_once("include/display_table.class.php");
include_once("utility.class.php");
include_once("dbconst.class.php");
class investment_report extends dbop
{
	public $actionPage = "../society.php";
	public $m_dbConn;
	public $obj_utility;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg = new display_table($this->m_dbConn);
		$this->obj_utility = new utility($this->m_dbConn);
	}


public function get_investmentregister_details($from_date,$to_date)
{
  $sql="SELECT l.id,l.ledger_name ,fd.id,fd.fdr_no,fd.deposit_date,fd.maturity_date,fd.int_rate,fd.principal_amt,fd.maturity_amt,fd.accrued_interest_legder,fd.interest_accrued ,fd.interest_legder,fd.interest,fd.note from ledger as l  LEFT JOIN fd_master as fd ON l.id = fd.LedgerID where society_id=".$_SESSION['society_id']." and fd.deposit_date between '".getDBFormatDate($from_date)."' and '".getDBFormatDate($to_date)."'";
   $result=$this->m_dbConn->select($sql);
  
   return $result;	

}
 public function GetTDSReceivable($fd_id)
 	{
		
		$vNumber ='';
		$data=  array();
     	$sql02 = "SELECT * FROM `ledger` where ledger_name like '%TDS Receivable%'";
 	 	$res2 = $this->m_dbConn->select($sql02);
 	 	for($i=0; $i<sizeof($res2);$i++)
 	 	{
 		 	$ledgerID .= $res2[$i]['id'].',';
 		 	$ledgername = $res2[$i]['ledger_name'];

			
		}
		
		$ldata= rtrim($ledgerID,',');


 		$sql01 = "SELECT Distinct(`VoucherNo`) as voucher_no FROM `voucher` where `RefTableID` = 6 and `RefNo` = '".$fd_id."'";
	 	$res1 = $this->m_dbConn->select($sql01);
		if($res1 <> '')
 		{
			for($v=0;$v< sizeof($res1);$v++)
			{
				$voucherNo .=$res1[$v]['voucher_no'].',';
 			}
		
			$vNumber = rtrim($voucherNo,',');
		
		    $sql03 ="SELECT * FROM `voucher` where `VoucherNo` IN(".$vNumber.") AND (`By` IN(".$ldata.") OR `TO` IN(".$ldata.")) group by VoucherNo";
		    $sql03 ="SELECT SUM(Debit) as TDSAmount FROM `voucher` where `VoucherNo` IN(".$vNumber.") AND (`By` IN(".$ldata.") OR `TO` IN(".$ldata.")) ";
				
			$res3 = $this->m_dbConn->select($sql03);
		    return $res3[0]['TDSAmount'];	

		
		}
	
 	}

	 public function GetAccrudAmount($fd_id,$acc_ledgerID)
	 {
		 $YearDate =$this->GetYearDateAndDesc($_SESSION['society_creation_yearid']);  // Added ne Condition 
		 $vNumber ='';
		 $data=  array(); 
		 // Changes On Accrud Int Ledger 
		 //$sql02 = "SELECT * FROM `ledger` where ledger_name like '%Accrued%' OR ledger_name like '%Accured%'";
		 $sql02 = "SELECT * FROM `ledger` where id= '".$acc_ledgerID."'";
		  $res2 = $this->m_dbConn->select($sql02);
		  
			 $ledgerID = $res2[0]['id'];
			  $ledgername = $res2[0]['ledger_name'];
		 
		  $sql01 = "SELECT Distinct(`VoucherNo`) as voucher_no FROM `voucher` where `RefTableID` = 6 and `RefNo` = '".$fd_id."'";
		  $res1 = $this->m_dbConn->select($sql01);
		 if($res1 <> '')
		 {
			 for($v=0;$v< sizeof($res1);$v++)
			 {
				 $voucherNo .=$res1[$v]['voucher_no'].',';
			 }
		 
			 $vNumber = rtrim($voucherNo,',');
			 
				 //$sql03 ="SELECT * FROM `voucher` where `VoucherNo` IN(".$vNumber.") AND (`By` IN(".$ldata.") OR `TO` IN(".$ldata."))  group by VoucherNo";
				 $sql03 ="SELECT * FROM `voucher` where `VoucherNo` IN(".$vNumber.") AND (`By` ='".$ledgerID."' OR `TO`='".$ledgerID."')  group by VoucherNo";
				 //SELECT * FROM `voucher` where `VoucherNo` IN(26907,26908) AND (`By` ='305' OR `TO`='305') and `Date`between '2020-04-01' and '2021-03-31' group by VoucherNo LIMIT 1 , 1
				 $res3 = $this->m_dbConn->select($sql03);
				 
				 
				 $cnt = 0;
				 $finalData = array();
				 for($i = 0 ; $i < sizeof($YearDate); $i++)
				 {
					 
					 for($j=0; $j<sizeof($res3);$j++)
					 {
						 if($res3[$j]['Date'] >= $YearDate[$i]['BegninigDate'] && $res3[$j]['Date'] <= $YearDate[$i]['EndingDate'] )
						 {
								 
							 $finalData[$i][$cnt] = $res3[$j];
						 }
						  else
						  {
							  continue;
						 
						  }
					 }
					 
				 }
				 
				 
			 return $finalData;	
		 }
	 }



	 public function GetYearDateAndDesc($startYearID)
	 {
		 $yearData=array();
		 $sql =" SELECT * FROM `year` where YearID >= '".$startYearID."'";
		 $res = $this->m_dbConn->select($sql);
		 for($i=0; $i<sizeof($res); $i++)
		 {
			 array_push($yearData, array("YearDesc"=> $res[$i]['YearDescription'], "BegninigDate"=>$res[$i]['BeginingDate'],"EndingDate"=>$res[$i]['EndingDate']));
		 }
		 return  $yearData;
		
	 } 
}
?>
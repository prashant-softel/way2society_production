
<?php include_once("../classes/FixedDeposit.class.php");
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
$obj_FixedDeposit = new FixedDeposit($dbConn);

//remove all empty spaces after php closing brackets
ob_clean();

if(isset($_REQUEST['getbalance']))
{
	$balance = $obj_FixedDeposit->getOpeningBalance($_REQUEST['ledger']);
}
else if($_REQUEST["method"] == "fetch_vouchers")
{
	$ledger_id = $_REQUEST["ledgerid"];
	$vouchers = $obj_FixedDeposit->show_Vouchers($ledger_id);

	echo $vouchers;
}
else if($_REQUEST["method"]=="edit" || $_REQUEST["method"]=="delete" || $_REQUEST["method"]=="renew")
{
	echo $_REQUEST["method"]."@@@";
	
	if($_REQUEST["method"]=="edit" || $_REQUEST["method"]=="renew")
	{
		$select_type = $obj_FixedDeposit->selecting();
		echo json_encode($select_type);
		
		//echo "test";
		/*foreach($select_type as $k => $v)
		{
			foreach($v as $kk => $vv)
			{
				echo $vv."#";
			}
		}*/
	}
	
	if($_REQUEST["method"]=="delete")
	{
		$obj_FixedDeposit->deleting();
		return "Data Deleted Successfully";
	}
}

		
if($_REQUEST["method"] == 'fetchTable')
{
	echo $result = $obj_FixedDeposit->pgnation($_REQUEST["fetchType"]);
}

if($_REQUEST["method"] == 'fetchReport')
{
	$ledgerIDArray = json_decode(str_replace('\\', '', $_REQUEST['ledgerIDArray']), true);
	$status = $_REQUEST['status'];
	
	
	//echo "<div><center><font>BILL SUMMARY WITH BIFURCATION</font></center></div>";	
		
	/*for($i = 0 ;$i < sizeof($ledgerIDArray); $i++)
	{
		$sql = "select `ledger_name` from `ledger` where `id` = '".$ledgerIDArray[$i]."' " ;
		$res = $dbConn->select($sql);
		//echo "<br/><br/><div  id='ledgername'><center><h3><font>"  .$res[0]['ledger_name']. "</font></h3></center></div>";	
		*/
		array_push($ledgerIDArray, '0');		
		$ledgerIDArray = implode(',', $ledgerIDArray);
		$obj_FixedDeposit->fetchRecords($ledgerIDArray,$status);
	//}
}

if($_REQUEST['method'] == 'getledger')	
{
	if($_REQUEST['category_id'] == 0)
	{
		
		$fdAccountArray =  $obj_FixedDeposit->FetchFdCategories();
		$fdAccountArray = implode(',', $fdAccountArray);
		echo $fd_purpose = $obj_FixedDeposit->comboboxForReport($_REQUEST['status_id'] ,"select `id`, ledgertable.ledger_name as ledger_name from `ledger` as ledgertable Join `account_category` as categorytbl on  categorytbl.category_id=ledgertable.categoryid where   ledgertable.categoryid IN (".$fdAccountArray.") and society_id = '".$_SESSION['society_id']."' ","id"); 
	}
	else
	{
		//echo "select `id`,concat_ws(' - ', ledgertable.ledger_name,ledgertable.id) as ledger_name from `ledger` as ledgertable Join `account_category` as categorytbl on  categorytbl.category_id=ledgertable.categoryid where  ledgertable.categoryid ='".$_REQUEST['category_id'] ."' and society_id = '".$_SESSION['society_id']."' ";
			echo $fd_purpose = $obj_FixedDeposit->comboboxForReport($_REQUEST['status_id'] ,"select `id`,ledgertable.ledger_name as ledger_name from `ledger` as ledgertable Join `account_category` as categorytbl on  categorytbl.category_id=ledgertable.categoryid where  ledgertable.categoryid ='".$_REQUEST['category_id'] ."' and society_id = '".$_SESSION['society_id']."' ","id"); 
	}
}
if($_REQUEST['method'] == 'ValidateOpening')	
{
	$validate = 0;
	$finalArray =array();
	$depositeDate = $_REQUEST['DateOfDeposite'];
	$qry = "SELECT DATE_FORMAT(`BeginingDate`, '%d-%m-%Y') as BeginingDate,DATE_FORMAT(`EndingDate`, '%d-%m-%Y') as EndingDate,YearID  FROM `year` where YearID ='".$_SESSION['default_year']."'";
	$res =$dbConn->select($qry);
	$begningDate = $res[0]['BeginingDate'];
	$EndDate = $res[0]['EndingDate'];
	
	if($depositeDate <> '')
	{ 
	
		if((strtotime($begningDate) <= strtotime($depositeDate)) && (strtotime($depositeDate) <= strtotime($EndDate)))
		{
			
			$validate =1;   // Same year entry 
		}
		else
		{
			$validate = 2;  // year missmatch 
			
		}
	}
	else
	{
		$validate = 0; // deposite date null
	}
	$qry1 = "SELECT * FROM `year` where YearID ='".$_SESSION['society_creation_yearid']."'";
	$res1 =$dbConn->select($qry1);
	array_push($finalArray,array("validate"=>$validate,"YearDesc"=>$res1[0]['YearDescription'],"BegDate"=>$res1[0]['BeginingDate'],"End"=>$res1[0]['EndingDate']));
	
	echo json_encode($finalArray);
}
?>
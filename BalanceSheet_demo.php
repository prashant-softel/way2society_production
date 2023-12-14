<?php 
//include_once "../ses_set_s.php"; 
include_once ("classes/include/dbop.class.php");
	$dbConn = new dbop();
include_once "classes/dbconst.class.php";
include "classes/include/fetch_data.php";
include "classes/BalanceSheet.class.php";
$parent=0;
$objLIABILITY=new BalanceSheet($dbConn);
$objASSET=new BalanceSheet($dbConn);
$objINCOME=new BalanceSheet($dbConn);
$objEXPENSE=new BalanceSheet($dbConn);
$objFetchData = new FetchData($dbConn);
$OpeningBalance=$objLIABILITY->OpeningBalanceCalculate();
$LIABILITYData=$objLIABILITY->CategoryArray(LIABILITY);
//print_r($LIABILITYData);
$ASSETData=$objASSET->CategoryArray(ASSET);
$ASSETData=$objLIABILITY->ArrayShifting(LIABILITY,$LIABILITYData,$ASSETData);
//print_r($ASSETData);
$LIABILITYData=$objLIABILITY->UnsetArray($LIABILITYData);
$LIABILITYData=$objASSET->ArrayShifting(ASSET,$ASSETData,$LIABILITYData);
$ASSETData=$objASSET->UnsetArray($ASSETData);
//print_r($LIABILITYData);
//print_r($ASSETData);
$LIABILITYTotal=$objLIABILITY->getTotal($LIABILITYData);
$ASSETTotal=$objASSET->getTotal($ASSETData);
$INCOMEData=$objINCOME->CategoryArray(INCOME);
$EXPENSEData=$objEXPENSE->CategoryArray(EXPENSE);
$INCOMEcredit=$objINCOME->CreditTotal;
$INCOMEdebit=$objINCOME->DebitTotal;
$incomeTotal=abs($INCOMEcredit - $INCOMEdebit);
$EXPENSEcredit=$objEXPENSE->CreditTotal;
$EXPENSEdebit=$objEXPENSE->DebitTotal;
$expenseTotal= abs($EXPENSEdebit - $EXPENSEcredit);
//$OpeningBalance=6304468;
if($incomeTotal > $expenseTotal)
{
	
	$netIncome=$incomeTotal - $expenseTotal;
	$ProfitnLoss=$netIncome;
	$LIABILITYTotal = $LIABILITYTotal +  $ProfitnLoss;
	
}
else
{
	$netLoss=$expenseTotal - $incomeTotal;
	$ProfitnLoss=$netLoss;
	$LIABILITYTotal = $LIABILITYTotal  - $ProfitnLoss;
}
$objFetchData->GetSocietyDetails($_SESSION['society_id']);

//echo "<br><br><br>";
//print_r($ASSETData);
//echo "<br><br>";
//echo "<br><br>";
//print_r($LIABILITYData);
//print_r($objASSET->unsetArray);
//echo "<br><br>";
/*

for($i=0;$i < sizeof($ASSETData);$i++)
{
	$amount=$ASSETData[$i]['credit'] - $ASSETData[$i]['debit'];
	if($ASSETData[$i]['parent_id']==0 && $amount <0)
	{
		array_push($LIABILITYData,$ASSETData[$i]);
	//print_r($LiabilityArray);
	//unset($ASSETData[$i]);
	//die();
		array_push($unsetArray,$i);
	//echo "id:".$ASSETData[$i]['id'];
	$LIABILITYData=Calculate2($ASSETData[$i]['id'],$ASSETData,$unsetArray,$LIABILITYData);
		
	//print_r($ASSETData[$i]);
	//echo "<br><br>";
	
	}

}


function Calculate2($id ,$ASSETData,$unsetArray,$LIABILITYData)
{
	//$parent_id=0;
	
	for($i=0;$i < sizeof($ASSETData)-1;$i++)
	{
			if($ASSETData[$i]['parent_id']==$id)
			{
				//echo "id2:".$ASSETData[$i]['id'];
	//echo "<br>";	
				array_push($LIABILITYData,$ASSETData[$i]);
				array_push($unsetArray,$i);
				//unset($key);
			//print_r($key);
			$LIABILITYData=Calculate2($ASSETData[$i]['id'],$ASSETData,$unsetArray,$LIABILITYData);	
			}
		
	}
	return $LIABILITYData;	
	
}
foreach($unsetArray as $key=>$value)
{
	//echo "<br>".$value;
	unset($ASSETData[$value]);	
}
*/
//print_r($LIABILITYData);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>BALANCE SHEET</title>
<script  type="text/javascript" src="js/jsBalanceSheet.js"></script>
<script type="text/javascript" language="javascript" src="media/js/jquery.js"></script>
<link rel="stylesheet" type="text/css" href="css/bootstrap-combined.min.css" >
<link rel="stylesheet" type="text/css" href="css/treeview.css" >
<script language="javascript" src="js/bootstrap.min.js"></script>



</head>

<body>
<div style="border:1px solid black;" align="center">
        <div id="society_name" style="font-weight:bold; font-size:18px;"><?php echo $objFetchData->objSocietyDetails->sSocietyName; ?></div>
            <div id="society_reg" style="font-size:14px;"><?php if($objFetchData->objSocietyDetails->sSocietyRegNo <> "")
				{
					echo "Registration No. ".$objFetchData->objSocietyDetails->sSocietyRegNo; 
				}
				?></div>
            <div id="society_address"; style="font-size:14px;"><?php echo $objFetchData->objSocietyDetails->sSocietyAddress; ?></div>
        </div>
</div>
<h6 align="center">BALANCE SHEET AS ON <?php echo date("d-m-Y");?></h6>

<table border="1px"  align="center" width="90%"  style="border-collapse:separate; max-width:90%;">
<tr><td><b>Particulars</b>&nbsp;(Liabilities)<?php echo str_repeat("&nbsp;",50);?> <b>Closing Balance</b></td><td><b>Particulars</b>&nbsp;(Assets)<?php echo str_repeat("&nbsp;",50);?> <b>Closing Balance</b></td></tr>
<tr>
<td valign="top"><?php  
$first_key = reset($LIABILITYData);
//echo $first_key['parent_id'];
echo ('<div class="tree well">'.$objLIABILITY->generateBalanceSheet($LIABILITYData,$first_key['parent_id']));
echo '<ul><li><label><span style="border:none;width:320px;">Profit & Loss A/c</span><span style="border:none;width:100px;text-align:right;">'.number_format($ProfitnLoss,2).'</span></label></li></ul>';
//echo '<ul><li><label><span style="border:none;width:300px;">Profit & Loss A/c(O.B.)</span><span style="border:none;width:100px;padding-left:50px;">'.number_format($OpeningBalance,2).'</span></label></li></ul>';
echo '</div>';

?>

</td><td valign="top"><?php 

foreach($ASSETData as $key)
{
$parent= $ASSETData[$key]['parent_id'];
break;	
}
$first_key = reset($ASSETData);
//print_r($first_key['parent_id']);
echo ('<div class="tree well">'.$objASSET->generateBalanceSheet($ASSETData,$first_key['parent_id']));
echo '</div>';
?>

</td></tr>
<tr><td><b>Total <?php echo str_repeat("&nbsp;",80).number_format($LIABILITYTotal, 2);?></b></td><td><b>Total <?php echo str_repeat("&nbsp;",80).number_format($ASSETTotal, 2);?></b></td></tr>
<script type="text/javascript">
$(function () {
	//alert("test");
    $('.tree li:has(ul)').addClass('parent_li').find(' > label ').attr('title', 'Collapse this branch');
    $('.tree li.parent_li > label ').on('click', function (e) {
		//alert("parent");
        var children = $(this).parent('li.parent_li').find(' > ul > li');
        if (children.is(":visible")) {
            children.hide('fast');
            $(this).attr('title', 'Expand this branch').find(' > span').addClass('icon-plus-sign').removeClass('icon-minus-sign');
        } else {
            children.show('fast');
			
            $(this).attr('title', 'Collapse this branch').find(' > span').addClass('icon-minus-sign').removeClass('icon-plus-sign');
        }
        e.stopPropagation();
    });
});
</script>
</table>
<br />
<br />
<table align="center">
<tr><td><b>Chairman</b><?php echo str_repeat("&nbsp;",85);?></td><td><b>Secretary</b> <?php echo str_repeat("&nbsp;",85);?></td><td><b>Treasurer</b></td></tr>
</table>
</body>
</html>
<?php //include_once("../includes/foot.php"); ?>
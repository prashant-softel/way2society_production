<?php 
//include_once "../ses_set_s.php"; 
include_once ("classes/include/dbop.class.php");
	$dbConn = new dbop();
include_once "classes/dbconst.class.php";
include "classes/BalanceSheet.class.php";
include "classes/include/fetch_data.php";
$objFetchData = new FetchData($dbConn);
$objFetchData->GetSocietyDetails($_SESSION['society_id']);
$objINCOME=new BalanceSheet($dbConn);
$objEXPENSE=new BalanceSheet($dbConn);
$INCOMEData=$objINCOME->CategoryArray(INCOME);
$EXPENSEData=$objEXPENSE->CategoryArray(EXPENSE);
//$incomeTotal=$objBalanceSheet->getTotal($INCOMEData);
//$expenseTotal=$objBalanceSheet->getTotal($EXPENSEData);
$INCOMEcredit=$objINCOME->CreditTotal;
$INCOMEdebit=$objINCOME->DebitTotal;
$incomeTotal=abs($INCOMEcredit - $INCOMEdebit);
$EXPENSEcredit=$objEXPENSE->CreditTotal;
$EXPENSEdebit=$objEXPENSE->DebitTotal;
$expenseTotal=abs($EXPENSEdebit - $EXPENSEcredit);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>INCOME AND EXPENDITURE STATEMENT</title>
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
<h6 align="center">INCOME AND EXPENDITURE STATEMENT AS ON <?php echo date("d-m-Y");?></h6>
<table border="1px"  align="center" width="90%"  style="border-collapse:separate;">
<tr><td><b>Particulars</b>&nbsp;(Expense)<?php echo str_repeat("&nbsp;",30);?> <b>Credit</b><?php echo str_repeat("&nbsp;",25);?> <b>Debit</b></td><td><b>Particulars</b>&nbsp;(Income)<?php echo str_repeat("&nbsp;",30);?> <b>Credit</b><?php echo str_repeat("&nbsp;",25);?> <b>Debit</b></td></tr>
<tr>
<td valign="top">
<?php 
echo ('<div class="tree">'.$objEXPENSE->generateIncomeStatement($EXPENSEData,1));
if($incomeTotal > $expenseTotal)
{
$netIncome=$incomeTotal - $expenseTotal;
$expenseTotal=$expenseTotal + $netIncome;
echo '<ul><li><label><span style="border:none;width:300px;">Net Profit</span><span style="border:none;width:100px;padding-left:50px;text-align:right;">'.number_format($netIncome,2).'</span></label></li></ul>';
}
echo '</div>';?>
</td>
<td valign="top">
<?php 
echo ('<div class="tree">'.$objINCOME->generateIncomeStatement($INCOMEData,1));
if($expenseTotal > $incomeTotal)
{
$netLoss=$expenseTotal - $incomeTotal;
$incomeTotal=$incomeTotal + $netLoss;
echo '<ul><li><label><span style="border:none;width:300px;">Net Loss</span><span style="border:none;width:100px;padding-left:50px;text-align:right;">'.number_format($netLoss,2).'</span></label></li></ul>';
}
echo '</div>';?>
</td>
</tr>
<tr><td><b>Total <?php echo str_repeat("&nbsp;",100).number_format($expenseTotal, 2);?></b></td><td><b>Total <?php echo str_repeat("&nbsp;",100).number_format($incomeTotal, 2);?></b></td></tr>
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
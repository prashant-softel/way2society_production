<?php 
//include_once "../ses_set_s.php"; 
include_once ("classes/include/dbop.class.php");
	$dbConn = new dbop();
include_once "classes/dbconst.class.php";
include "classes/include/fetch_data.php";
include "classes/opening_balance.class.php";
$objLIABILITY=new  OpeningBalance($dbConn);
$objASSET=new  OpeningBalance($dbConn);
$objINCOME=new  OpeningBalance($dbConn);
$objEXPENSE=new  OpeningBalance($dbConn);
$objFetchData = new FetchData($dbConn);
$OpeningBalance=$objLIABILITY->OpeningBalanceCalculate();
$LIABILITYData=$objLIABILITY->CategoryArray(LIABILITY);
$ASSETData=$objASSET->CategoryArray(ASSET);
$LIABILITYTotal=$objLIABILITY->getTotal($LIABILITYData);
$ASSETTotal=$objASSET->getTotal($ASSETData);
$objFetchData->GetSocietyDetails($_SESSION['society_id']);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>OPENING BALANCE</title>
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
<h6 align="center">OPENING BALANCE</h6>

<table border="1px"  align="center" width="90%"  style="border-collapse:separate;">
<tr><td><b>Particulars</b>&nbsp;(Liabilities)<?php echo str_repeat("&nbsp;",60);?> <b>Closing Balance</b></td><td><b>Particulars</b>&nbsp;(Assets)<?php echo str_repeat("&nbsp;",60);?> <b>Closing Balance</b></td></tr>
<tr>
<td valign="top"><?php echo ('<div class="tree well">'.$objLIABILITY->generateBalanceSheet($LIABILITYData,$LIABILITYData[0]['parent_id']));
echo '</div>';
?>

</td><td valign="top"><?php echo ('<div class="tree well">'.$objASSET->generateBalanceSheet($ASSETData,$ASSETData[0]['parent_id']));
echo '</div>';
?>

</td></tr>
<tr><td><b>Total <?php echo str_repeat("&nbsp;",85).number_format($LIABILITYTotal, 2);?></b></td><td><b>Total <?php echo str_repeat("&nbsp;",85).number_format($ASSETTotal, 2);?></b></td></tr>
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

<!--<table align="center">
<tr><b>OPENING BALANCE:&nbsp;</b><?php //echo $OpeningBalance;?></tr>
<tr><td><b>Chairman</b><?php //echo str_repeat("&nbsp;",85);?></td><td><b>Secretary</b> <?php //echo str_repeat("&nbsp;",85);?></td><td><b>Member</b></td></tr>
</table>-->
</body>
</html>
<?php //include_once("../includes/foot.php"); ?>
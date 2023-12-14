<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Opening Balance</title>
</head>

<?php 
include_once "ses_set_s.php"; 
include_once("includes/head_s.php"); 
include_once ("classes/include/dbop.class.php");
$dbConn = new dbop();
$dbConnRoot = new dbop(true); 
include_once "classes/dbconst.class.php";
include "classes/opening_balance.class.php";
include_once("classes/utility.class.php");

$objLIABILITY = new  OpeningBalance($dbConn);
$objASSET = new  OpeningBalance($dbConn);
$objINCOME = new  OpeningBalance($dbConn);
$objEXPENSE = new  OpeningBalance($dbConn);
$objFetchData = new FetchData($dbConn);
$objUtility   = new utility($dbConn,$dbConnRoot);
$societyDetails = $objUtility->GetSocietyInformation($_SESSION['society_id']);
$bill_format = $societyDetails['balancesheet_template'];

$LIABILITYData = $objLIABILITY->CategoryArray(LIABILITY);
$ASSETData = $objASSET->CategoryArray(ASSET);

if($bill_format == ABSOLUTE_BALANCESHEET)
{
	$ASSETData = $objLIABILITY->ArrayShifting(LIABILITY,$LIABILITYData,$ASSETData);
	$LIABILITYData = $objLIABILITY->UnsetArray($LIABILITYData);

	$LIABILITYData = $objASSET->ArrayShifting(ASSET,$ASSETData,$LIABILITYData);
	$ASSETData = $objASSET->UnsetArray($ASSETData);
}

$LIABILITYData = $objLIABILITY->CalculateTotal($LIABILITYData);
$ASSETData = $objASSET->CalculateTotal($ASSETData);

$LIABILITYTotal = $objLIABILITY->getTotal($LIABILITYData);
$ASSETTotal = $objASSET->getTotal($ASSETData);

$INCOMEData = $objINCOME->CategoryArray(INCOME);
$EXPENSEData = $objEXPENSE->CategoryArray(EXPENSE);

$INCOMEcredit = $objINCOME->CreditTotal;
$INCOMEdebit = $objINCOME->DebitTotal;

//$incomeTotal = abs($INCOMEcredit - $INCOMEdebit);
$incomeTotal = $INCOMEcredit - $INCOMEdebit;

$EXPENSEcredit = $objEXPENSE->CreditTotal;
$EXPENSEdebit = $objEXPENSE->DebitTotal;


//$expenseTotal = abs($EXPENSEdebit  - $EXPENSEcredit);
$expenseTotal = $EXPENSEdebit  - $EXPENSEcredit;

if($incomeTotal > $expenseTotal)
{
	$netIncome = $incomeTotal - $expenseTotal;
	$ProfitnLoss = $netIncome;
	$LIABILITYTotal = $LIABILITYTotal +  $netIncome;
	
}
else
{
	$netLoss = $expenseTotal - $incomeTotal;
	$ProfitnLoss = '-'.$netLoss; // show - sign for loss
	$LIABILITYTotal = $LIABILITYTotal  - $netLoss;
}

$objFetchData->GetSocietyDetails($_SESSION['society_id']);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>OPENING BALANCE</title>
<script  type="text/javascript" src="js/jsBalanceSheet.js"></script>
<script type="text/javascript" language="javascript" src="media/js/jquery.js"></script>
<link rel="stylesheet" type="text/css" href="css/treeview.css" >
<script language="javascript" src="js/bootstrap.min.js"></script>
<style>
.ExportStyle
{
	border-collapse: collapse;padding-left:50px;
}
.rowstyle > th{border:1px dashed gray !important;  }
.Exportdiv{ display:none;}
</style>
</head>

<body>

<br/>
<div class="panel panel-info" id="panel" style="display:none;">
    <div class="panel-heading" id="pageheader">OPENING BALANCE</div>
    <br />
   <!-- <div style="padding-left: 15px;"><button type="button" class="btn btn-primary btn-circle" onClick="history.go(-1);" style="float:left;"><i class="fa  fa-arrow-left"></i></button></div>-->
<center><font style="font-size:14px;"><b>OPENING BALANCE</b></font></center>
<br/>
<br/>
<div style="padding-left: 15px;">
 <INPUT TYPE="button" VALUE="Expand All" onClick="ExpandALL();"    class="btn btn-primary"   style="width:10%;box-shadow: none;"/>
<INPUT TYPE="button" VALUE="Collapse All" onClick="CollapseALL();"   class="btn btn-primary"   style="width:10%;box-shadow: none;"/>
<?php //if($_SESSION['feature'][CLIENT_FEATURE_EXPORT_MODULE] == 1){?>
<input  type="button" id="btnExport1" value=" Export To Excel"  style="width:150px;box-shadow: none;" class="btn btn-primary"   onclick="ExportToExcel(this.id);"/>
<?php //} ?>
</div>
<br />
<br />
<center>
<table  align="center" width="90%"  style="max-width:90%;border:1px solid black;">
<tr>
	<td style="font-size:14px;">
    	<div style="width:100%;">
        	<div style="border:1px solid gray !important; width:50%;float:left;text-align:center;"><b>Particulars</b>&nbsp;(Liabilities)</div>
            <div style="border:1px solid gray !important; width:50%;float:left;text-align:center; "> <b>Closing Balance</b>&nbsp;(Rs.)</div>
        </div>
     </td>
     <td style="font-size:14px;">
         <div style="width:100%;">
            <div style="border:1px solid gray !important;width:50%;float:left;text-align:center; "><b>Particulars</b>&nbsp;(Assets)</div>
            <div style="border:1px solid gray !important; width:50%;float:left;text-align:center; "> <b>Closing Balance</b>&nbsp;(Rs.)</div>
         </div>
     </td>
<tr>

<tr>
    <td valign="top" style="border-right:1px solid black;">
        <?php 
        echo ('<div class="tree well">'.$objLIABILITY->generateOpeningBalanceTree($LIABILITYData,$LIABILITYData[0]['parent_id'],LIABILITY));
		echo '<ul><li><label><span style="border:none;width:270px;"><a href = "IncomeStmt.php" target="_blank">Profit & Loss A/c</a></span><span style="border:none;width:50px;text-align:right;"><a href = "IncomeStmt.php" target="_blank">'.number_format($ProfitnLoss,2).'</a></span></label></li></ul>';
		 echo '</div>';
        ?>
    </td>
    <td valign="top">
        <?php 
        echo ('<div class="tree well">'.$objASSET->generateOpeningBalanceTree($ASSETData,$ASSETData[0]['parent_id'],ASSET));
        echo '</div>';
        ?>
    </td>
</tr>
<tr>
    <td style="font-size:14px;">
    	<div style="width:100%;">
        	<div style="border:1px solid gray !important; width:50%;float:left;text-align:center;"><b>Total</b>&nbsp;(Rs.)</div>
            <div style="border:1px solid gray !important; width:50%;float:left;text-align:center; "> <b><?php echo number_format($LIABILITYTotal, 2);?></b></div>
        </div>
     </td>
     <td style="font-size:14px;">
         <div style="width:100%;">
            <div style="border:1px solid gray !important;width:50%;float:left;text-align:center; "><b>Total</b>&nbsp;(Rs.)</div>
            <div style="border:1px solid gray !important; width:50%;float:left;text-align:center; "> <b><?php echo number_format($ASSETTotal, 2);?></b></div>
         </div>
     </td>
</tr>

<script type="text/javascript">
$(function () 
{
	$('.tree li:has(ul)').addClass('parent_li').find(' > label ').attr('title', 'Collapse this branch');
    $('.tree li.parent_li > label ').on('click', function (e) {
		var children = $(this).parent('li.parent_li').find(' > ul > li');
        
		if(children.is(":visible")) 
		{
            children.hide('fast');
            $(this).attr('title', 'Expand this branch').find(' > span').addClass('icon-plus-sign').removeClass('icon-minus-sign');
        } 
		else 
		{
            children.show('fast');
			$(this).attr('title', 'Collapse this branch').find(' > span').addClass('icon-minus-sign').removeClass('icon-plus-sign');
        }
		
        e.stopPropagation();
    });
});
</script>



</table>
</center>
<br />
<br />
<div style="padding-left: 15px;">
<INPUT TYPE="button" VALUE="Expand All" onClick="ExpandALL();"    class="btn btn-primary"   style="width:10%;box-shadow: none;" />
<INPUT TYPE="button" VALUE="Collapse All" onClick="CollapseALL();"    class="btn btn-primary"   style="width:10%;box-shadow: none;" />
<?php //if($_SESSION['feature'][CLIENT_FEATURE_EXPORT_MODULE] == 1){?>
<input  type="button" id="btnExport1" value=" Export To Excel"  style="width:150px;box-shadow: none;" class="btn btn-primary"   onclick="ExportToExcel(this.id);"/>
<?php //}?>
</div>
<br />
<br />


<!-- code for export trial balance to excel-->
<div id="Exportdiv" class="Exportdiv">
<center>
<div>
	<div id="bill_header" style="text-align:center;">
            <div id="society_name" style="font-weight:bold; font-size:18px;"><?php echo $objFetchData->objSocietyDetails->sSocietyName; ?></div>
            <div id="society_reg" style="font-size:14px;"><?php if($objFetchData->objSocietyDetails->sSocietyRegNo <> "")
				{
					echo "Registration No. ".$objFetchData->objSocietyDetails->sSocietyRegNo; 
				}
				?></div>
            <div id="society_address"; style="font-size:14px;"><?php echo $objFetchData->objSocietyDetails->sSocietyAddress; ?></div>
        </div>
        <div id="bill_subheader" style="text-align:center;">
            <div style="font-weight:bold; font-size:16px;">OPENING BALANCE FROM <?php echo getDisplayFormatDate($_SESSION['from_date']); ?> TO <?php echo getDisplayFormatDate($_SESSION['to_date']);?></div>
        </div>
<table  align="center"   width="100%" id="Exportdata" class="ExportStyle" style="border:1px dashed gray !important;">
    <tr class="rowstyle">
      <th style="text-align:left;font-size:14px;border:1px dashed gray !important; width:300px;" >Particulars(Expense)</th>
      <th style="text-align:center;font-size:14px;border:1px dashed gray !important;width:200px;"  colspan="2">Amount(Rs.)</th>
      <th style="text-align:left;font-size:14px;border:1px dashed gray !important;width:300px;">Particulars(Income)</th>
      <th style="text-align:center;font-size:14px;border:1px dashed gray !important;width:200px;" colspan="2">Amount(Rs.)</th>
    </tr>
    <tr class="rowstyle">
    	<td colspan="3">
    		<table>
            	<?php 
					echo $objEXPENSE->generateOpeningBalanceTreeTable($LIABILITYData,$LIABILITYData[0]['parent_id'],LIABILITY);
					echo '<tr><label><td style="width:490px;border-right:1px solid black;border-left:1px solid black;"><span><b>Profit & Loss A/c</b></span></td><td style="text-align:right;width:100px;color:blue;border-right:1px solid black;"><span><u></u></span></td><td style="text-align:right;width:100px;color:blue;border-right:1px solid black;"><span><u>'.number_format($ProfitnLoss,2).'</u></span></td></label></tr>';
				?>
                <tr>
                	<td style="border-top:1px solid black;"></td>
                    <td style="border-top:1px solid black;"></td>
                    <td style="border-top:1px solid black;"></td>
                </tr>
			 </table>
        </td>
       <td colspan="3">
        	<table>
				<?php 
					echo $objEXPENSE->generateOpeningBalanceTreeTable($ASSETData,$ASSETData[0]['parent_id'],ASSET);
				?>
            </table>
        </td>
    </tr>
    <tr class="rowstyle">
    	<td style="font-size:14px;border:1px dashed gray !important;"><b>TOTAL(Rs.)</b></td>
        <td style="text-align:right;font-size:14px;border:1px dashed gray !important;"><b><?php echo number_format($LIABILITYTotal, 2);?></b></td>
        <td style="text-align:right;font-size:14px;border:1px dashed gray !important;"></td>
        <td style="font-size:14px;border:1px dashed gray !important;"><b>TOTAL(Rs.)</b></td>
        <td style="text-align:right;font-size:14px;border:1px dashed gray !important;"><b><?php echo number_format($ASSETTotal, 2);?></b></td>
        <td style="text-align:right;font-size:14px;border:1px dashed gray !important;"></td>
    </tr>
</table>
</div>
</center>
</div>
</div>

<?php include_once "includes/foot.php"; ?>
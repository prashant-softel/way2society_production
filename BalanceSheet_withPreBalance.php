<?php 
include_once "ses_set_s.php"; 
include_once("includes/head_s.php");
include_once ("classes/include/dbop.class.php");
$dbConn = new dbop();
$dbConnRoot = new dbop(true); 
include_once "classes/dbconst.class.php";
include "classes/BalanceSheet_withPreBalance.class.php";
include_once("classes/utility.class.php");
$parent=0;

$objLIABILITY = new BalanceSheet($dbConn);
$objASSET = new BalanceSheet($dbConn);
$objINCOME = new BalanceSheet($dbConn);
$objEXPENSE = new BalanceSheet($dbConn);
$objFetchData = new FetchData($dbConn);
$objUtility   = new utility($dbConn,$dbConnRoot);
$societyDetails = $objUtility->GetSocietyInformation($_SESSION['society_id']);
$bill_format = $societyDetails['balancesheet_template'];

$PreviuousYearOpeningdate =date('Y-m-d', strtotime($_SESSION['default_year_start_date'].' -1 year'));
$PreviousFromDate = date('Y-m-d', strtotime($_SESSION['from_date'].' -1 year'));
$PreviousEndDate = date('Y-m-d', strtotime($_SESSION['to_date'].' -1 year'));

$LIABILITYData = $objLIABILITY->CategoryArray(LIABILITY, getDBFormatDate($_SESSION['from_date']), getDBFormatDate($_SESSION['to_date']));
$ASSETData = $objASSET->CategoryArray(ASSET, getDBFormatDate($_SESSION['from_date']), getDBFormatDate($_SESSION['to_date']));

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

$INCOMEData = $objINCOME->CategoryArray(INCOME, getDBFormatDate($_SESSION['from_date']), getDBFormatDate($_SESSION['to_date']));
$EXPENSEData = $objEXPENSE->CategoryArray(EXPENSE, getDBFormatDate($_SESSION['from_date']), getDBFormatDate($_SESSION['to_date']));


$ProfitandLossDetails = $objINCOME->ProfitandLoss($objINCOME->CreditTotal,$objINCOME->DebitTotal,$objINCOME->OpeningBalanceCreditTotal,$objINCOME->OpeningBalanceDebitTotal,$objEXPENSE->CreditTotal,$objEXPENSE->DebitTotal,$objEXPENSE->OpeningBalanceCreditTotal,$objEXPENSE->OpeningBalanceDebitTotal,$LIABILITYTotal);
$ProfitnLoss = $ProfitandLossDetails['ProfitnLoss'];
$LIABILITYTotal = $ProfitandLossDetails['LIABILITYTotal'];
$OpeningBalance = $ProfitandLossDetails['LastYearBalance'];
$CurrentBalance = $ProfitandLossDetails['CurrentYearBalance'];


//*************Previous Year****************************************************

if(isset($_REQUEST['show_PreBalance']))
{
	unset($objLIABILITY->unsetArray);
	unset($objASSET->unsetArray);
	$objLIABILITY->unsetArray = array();
	$objASSET->unsetArray = array();
	
	$objINCOME->CreditTotal = 0;
	$objINCOME->DebitTotal = 0;
	$objINCOME->OpeningBalanceCreditTotal = 0;
	$objINCOME->OpeningBalanceDebitTotal = 0;
	$objEXPENSE->CreditTotal = 0;
	$objEXPENSE->DebitTotal = 0;
	$objEXPENSE->OpeningBalanceCreditTotal = 0;
	$objEXPENSE->OpeningBalanceDebitTotal = 0;
	
	
	$PreviousLIABILITYData = $objLIABILITY->CategoryArray(LIABILITY, getDBFormatDate($PreviousFromDate), getDBFormatDate($PreviousEndDate));
	
	
	
	$PreviousASSETData = $objLIABILITY->CategoryArray(ASSET, getDBFormatDate($PreviousFromDate), getDBFormatDate($PreviousEndDate));
	
	if($bill_format == ABSOLUTE_BALANCESHEET)
	{
		$PreviousASSETData = $objLIABILITY->ArrayShifting(LIABILITY,$PreviousLIABILITYData,$PreviousASSETData);
		$PreviousLIABILITYData = $objLIABILITY->UnsetArray($PreviousLIABILITYData);
		
		$PreviousLIABILITYData = $objASSET->ArrayShifting(ASSET,$PreviousASSETData,$PreviousLIABILITYData);
		$PreviousASSETData = $objASSET->UnsetArray($PreviousASSETData);
	}

	$PreviousLIABILITYData = $objLIABILITY->CalculateTotal($PreviousLIABILITYData);
	$PreviousASSETData = $objASSET->CalculateTotal($PreviousASSETData);
	
	$PreviousLIABILITYTotal = $objLIABILITY->getTotal($PreviousLIABILITYData);
	
	
	$PreviousAssetTotal = $objASSET->getTotal($PreviousASSETData);
	
	$PreviousYearINCOMEData = $objINCOME->CategoryArray(INCOME, getDBFormatDate($PreviousFromDate), getDBFormatDate($PreviousEndDate));
	$PreviousYearEXPENSEData = $objEXPENSE->CategoryArray(EXPENSE, getDBFormatDate($PreviousFromDate), getDBFormatDate($PreviousEndDate));
	
	
	$PreviousProfitandLossDetails = $objINCOME->ProfitandLoss($objINCOME->CreditTotal,$objINCOME->DebitTotal,$objINCOME->OpeningBalanceCreditTotal,$objINCOME->OpeningBalanceDebitTotal,$objEXPENSE->CreditTotal,$objEXPENSE->DebitTotal,$objEXPENSE->OpeningBalanceCreditTotal,$objEXPENSE->OpeningBalanceDebitTotal,$PreviousLIABILITYTotal);
	$PreviousYearProfitnLoss = $PreviousProfitandLossDetails['ProfitnLoss'];
	$PreviousYearLIABILITYTotal = $PreviousProfitandLossDetails['LIABILITYTotal'];
	$PreviousYearOpeningBalance = $PreviousProfitandLossDetails['LastYearBalance'];
	$PreviousYearCurrentBalance = $PreviousProfitandLossDetails['CurrentYearBalance'];
	
	
	//**********************Final**********************************************
	
	
	$LIABILITYData = $objLIABILITY->MergeCurrentAndPreviousYearData($PreviousLIABILITYData,$LIABILITYData);
	$ASSETData = $objASSET->MergeCurrentAndPreviousYearData($PreviousASSETData,$ASSETData);

	
}

$objFetchData->GetSocietyDetails($_SESSION['society_id']);

?>

<html>
<head>
<title>BALANCE SHEET</title>
<script  type="text/javascript" src="js/jsBalanceSheet.js"></script>
<script type="text/javascript" language="javascript" src="media/js/jquery.js"></script>
<link rel="stylesheet" type="text/css" href="css/treeview.css" >
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


<br>
<div class="panel panel-info" id="panel" style="display:none;">
    <div class="panel-heading" id="pageheader">BALANCE SHEET</div>
   <br />
   <!--<div style="padding-left: 15px;"><button type="button" class="btn btn-primary btn-circle" onClick="history.go(-1);" style="float:left;"><i class="fa  fa-arrow-left"></i></button></div>-->
<center><font style="font-size:14px;"><b>Balance Sheet As Of <?php echo date_format(date_create($_SESSION['to_date']), 'd F Y');?></b></font></center>
<br />
<br />
<div style="padding-left: 15px;">
<INPUT TYPE="button" VALUE="Expand All" onClick="ExpandALL();"    class="btn btn-primary"   style="width:10%;box-shadow: none;" />
<INPUT TYPE="button" VALUE="Collapse All" onClick="CollapseALL();"    class="btn btn-primary"   style="width:10%;box-shadow: none;" />
<?php //if($_SESSION['feature'][CLIENT_FEATURE_EXPORT_MODULE] == 1){?>
<input  type="button" id="btnExport1" value=" Export To Excel"  style="width:150px;box-shadow: none;" class="btn btn-primary"   onclick="ExportToExcel(this.id);"/>
<!--<input  type="checkbox" id="chk_ShowPreviousYearBalanceSheet" name="chk_ShowPreviousYearBalanceSheet" value="0"  style="width:150px;box-shadow: none;"/>-->

<?php //}?> 
</div>
<br />
<br />
<center>
<table  align="center" width="90%"  style="max-width:90%;border:1px solid black;">
<?php if(isset($_REQUEST['show_PreBalance'])){?>
<tr>
	<td style="font-size:14px;">
    	<div style="width:100%;">
        	<div style="border:1px solid gray !important; width:30%;float:left;text-align:center; "> <b>Previous Balance</b>&nbsp;(Rs.)</div>
            <div style="border:1px solid gray !important; width:40%;float:left;text-align:center;height:42px;"><b>Particulars</b>&nbsp;(Liabilities)</div><?php //echo str_repeat("&nbsp;",70);?>
            <div style="border:1px solid gray !important; width:30%;float:left;text-align:center; "> <b>Closing Balance</b>&nbsp;(Rs.)</div>
        </div>
     </td>
     <td style="font-size:14px;">
     <div style="width:100%;">
     	<div style="border:1px solid gray !important; width:30%;float:left;text-align:center; "> <b>Previous Balance</b>&nbsp;(Rs.)</div>
     	<div style="border:1px solid gray !important;width:40%;float:left;text-align:center;height:42px;"><b>Particulars</b>&nbsp;(Assets)</div><?php //echo str_repeat("&nbsp;",70);?>
        <div style="border:1px solid gray !important; width:30%;float:left;text-align:center; "> <b>Closing Balance</b>&nbsp;(Rs.)</div>
     </div>
     </td>
<tr>
<?php }
  else{?>
<tr>
	<td style="font-size:14px;">
    	<div style="width:100%;">
        	<div style="border:1px solid gray !important; width:50%;float:left;text-align:center;"><b>Particulars</b>&nbsp;(Liabilities)</div><?php //echo str_repeat("&nbsp;",70);?>
            <div style="border:1px solid gray !important; width:50%;float:left;text-align:center; "> <b>Closing Balance</b>&nbsp;(Rs.)</div>
        </div>
     </td>
     <td style="font-size:14px;">
     <div style="width:100%;">
     	<div style="border:1px solid gray !important;width:50%;float:left;text-align:center; "><b>Particulars</b>&nbsp;(Assets)</div><?php //echo str_repeat("&nbsp;",70);?>
        <div style="border:1px solid gray !important; width:50%;float:left;text-align:center; "> <b>Closing Balance</b>&nbsp;(Rs.)</div>
     </div>
     </td>
<tr>
 <?php }?>
    <td valign="top" style="border-right:1px solid black;">
    <?php  
       
       if(isset($_REQUEST['show_PreBalance']))
		{
				echo ('<div class="tree well">'.$objLIABILITY->generateBalanceSheet_withPreviousyear($LIABILITYData,0,1));
      			echo '<ul><li><label style="width:100%;"><span style="border:none;width:24%;text-align:left;"><a href = "IncomeStmt.php">'.number_format($PreviousYearProfitnLoss,2).'</a></span><span style="border:none;width:36%;">Profit & Loss A/c</span><span style="border:none;width:30%;text-align:right;"><a href = "IncomeStmt.php">'.number_format($ProfitnLoss,2).'</a></span></label><ul ><li style="display:none;"><label style="width:100%"><span style="border:none;width:30%;text-align:left;font-weight:normal">'.number_format($PreviousYearOpeningBalance,2).'</span><span style="border:none;width:180px;font-weight:normal;" class="icon-minus-sign">Opening Balance</span><span style="border:none;width:50px;text-align:right;font-weight:normal;" class="icon-minus-sign">'.number_format($OpeningBalance,2).'</span></label></li></ul><ul ><li style="display:none;"><labelstyle="width="100%"><span style="border:none;width:30%;text-align:left;font-weight:normal">'.number_format($PreviousYearCurrentBalance,2).'</span><span style="border:none;width:180px;font-weight:normal;">Current Year</span><span style="border:none;width:50px;text-align:right;font-weight:normal;" class="icon-minus-sign">'.number_format($CurrentBalance,2).'</span></label></li></ul></li></ul>';	  	
				echo '</div>';
		}
		else
		{
			 echo ('<div class="tree well">'.$objLIABILITY->generateBalanceSheet($LIABILITYData,0,1));
       		 echo '<ul><li><label><span style="border:none;width:270px;">Profit & Loss A/c</span><span style="border:none;width:50px;text-align:right;"><a href = "IncomeStmt.php" target = "_blank">'.number_format($ProfitnLoss,2).'</a></span></label><ul ><li style="display:none;"><label><span style="border:none;width:270px;font-weight:normal;" class="icon-minus-sign">Opening Balance</span><span style="border:none;width:50px;text-align:right;font-weight:normal;" class="icon-minus-sign">'.number_format($LastYearBalance,2).'</span></label></li></ul><ul ><li style="display:none;"><label><span style="border:none;width:270px;font-weight:normal;">Current Year</span><span style="border:none;width:50px;text-align:right;font-weight:normal;" class="icon-minus-sign">'.number_format($CurrentYearBalance,2).'</span></label></li></ul></li></ul>';
			 echo '</div>';
		}
		
    ?>
    </td>
    <td valign="top">
        <?php 
        foreach($ASSETData as $key)
        {
            $parent = $ASSETData[$key]['parent_id'];
            break;	
        }
        $first_key = reset($ASSETData);
		
		if(isset($_REQUEST['show_PreBalance']))
		{
			echo ('<div class="tree well">'.$objASSET->generateBalanceSheet_withPreviousyear($ASSETData,0,2));
		}
		else
		{
			echo ('<div class="tree well">'.$objASSET->generateBalanceSheet($ASSETData,0,2));
		}
		echo '</div>';
		?>
    
    </td>
</tr>
<?php if(isset($_REQUEST['show_PreBalance'])){ ?>
<tr>
	<td style="font-size:14px;">
    	<div style="width:100%;">
        	<div style="border:1px solid gray !important; width:30%;float:left;text-align:center; "> <b><?php echo number_format($PreviousYearLIABILITYTotal, 2);?></b></div>
        	<div style="border:1px solid gray !important; width:40%;float:left;text-align:center;"><b>Total</b>&nbsp;(Rs.)</div><?php //echo str_repeat("&nbsp;",70);?>
            <div style="border:1px solid gray !important; width:30%;float:left;text-align:center; "> <b><?php echo number_format($LIABILITYTotal, 2);?></b></div>
        </div>
    </td>
    <td style="font-size:14px;">
    	<div style="width:100%;">
       		<div style="border:1px solid gray !important; width:30%;float:left;text-align:center; "> <b><?php echo number_format($PreviousAssetTotal, 2);?></b></div>
        	<div style="border:1px solid gray !important; width:40%;float:left;text-align:center;"><b>Total</b>&nbsp;(Rs.)</div><?php //echo str_repeat("&nbsp;",70);?>
            <div style="border:1px solid gray !important; width:30%;float:left;text-align:center; "> <b><?php echo number_format($ASSETTotal, 2);?></b></div>
        </div>
    </td>
</tr>
<?php }
else
{ ?>
<tr>
	<td style="font-size:14px;">
    	<div style="width:100%;">
        	<div style="border:1px solid gray !important; width:50%;float:left;text-align:center;"><b>Total</b>&nbsp;(Rs.)</div><?php //echo str_repeat("&nbsp;",70);?>
            <div style="border:1px solid gray !important; width:50%;float:left;text-align:center; "> <b><?php echo number_format($LIABILITYTotal, 2);?></b></div>
        </div>
    </td>
    <td style="font-size:14px;">
    	<div style="width:100%;">
        	<div style="border:1px solid gray !important; width:50%;float:left;text-align:center;"><b>Total</b>&nbsp;(Rs.)</div><?php //echo str_repeat("&nbsp;",70);?>
            <div style="border:1px solid gray !important; width:50%;float:left;text-align:center; "> <b><?php echo number_format($ASSETTotal, 2);?></b></div>
        </div>
    </td>
</tr>	
<?php }?>
<script type="text/javascript">
$(function () {
	$('.tree li:has(ul)').addClass('parent_li').find(' > label ').attr('title', 'Collapse this branch');
    $('.tree li.parent_li > label ').on('click', function (e) {
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
</center>

<br />
<br />
<div style="padding-left: 15px;">
<INPUT TYPE="button" VALUE="Expand All" onClick="ExpandALL();"    class="btn btn-primary"   style="width:10%;box-shadow: none;" />
<INPUT TYPE="button" VALUE="Collapse All" onClick="CollapseALL();"    class="btn btn-primary"   style="width:10%;box-shadow: none;" />
<?php if($_SESSION['feature'][CLIENT_FEATURE_EXPORT_MODULE] == 1){?>
<input  type="button" id="btnExport1" value=" Export To Excel"  style="width:150px;box-shadow: none;" class="btn btn-primary"   onclick="ExportToExcel(this.id);"/>
<?php }?>
<?php if($LIABILITYTotal <> $ASSETTotal)
{?>
<INPUT TYPE="button" VALUE="Registers Validation" onClick="window.open('RegistersValidation.php','RegistersValidationpopup','type=width=700,height=600,scrollbars=yes');"  class="btn btn-primary"    style="box-shadow: none; float:right;margin-right: 9px;">
<INPUT TYPE="button" VALUE="Bank Validation" onClick="window.open('BankEntriesValidation.php','BankEntriesValidationpopup','type=width=700,height=600,scrollbars=yes');"  class="btn btn-primary"    style="box-shadow: none; float:right;margin-right: 2px;">
<?php } ?>
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
            <div style="font-weight:bold; font-size:16px;">BALANCE SHEET FROM <?php echo getDisplayFormatDate($_SESSION['from_date']); ?> TO <?php echo getDisplayFormatDate($_SESSION['to_date']);?></div>
        </div>
<table  align="center"   width="100%" id="Exportdata" class="ExportStyle" style="border:1px dashed gray !important;">
   	<?php if(isset($_REQUEST['show_PreBalance'])){ ?>
    <tr class="rowstyle">
  	  <th style="text-align:left;font-size:14px;border:1px dashed gray !important; width:300px;" >Previous Balance</b>&nbsp;(Rs.)</th>
      <th style="text-align:left;font-size:14px;border:1px dashed gray !important; width:300px;" >Particulars(Liabilities)</th>
      <th style="text-align:center;font-size:14px;border:1px dashed gray !important;width:200px;"  colspan="2">Closing Balance(Rs.)</th>
      <th style="text-align:left;font-size:14px;border:1px dashed gray !important; width:300px;" >Previous Balance</b>&nbsp;(Rs.)</th>
      <th style="text-align:left;font-size:14px;border:1px dashed gray !important;width:300px;">Particulars(Assets)</th>
      <th style="text-align:center;font-size:14px;border:1px dashed gray !important;width:200px;" colspan="2">Closing Balance(Rs.)</th>
    </tr>
    <tr class="rowstyle">
    	<td colspan="4" style="border:1px solid black">
    		<table>
            	
				<?php 
					echo $objLIABILITY->generateBalanceSheetTable_withPreviousYear($LIABILITYData,0,1);
				?>
            	<tr>
                	<td style="border:1px solid black;"><b><?php echo number_format($PreviousYearProfitnLoss,2);?></b></td>
                	<td style="border:1px solid black;"><b>Profit & Loss A/c (Rs.)</b></td>
                    <td style="border:1px solid black;"><b><?php echo number_format($ProfitnLoss,2);?></b></td>
                    <td style="border:1px solid black;"></td>
                   
                </tr>
             </table>
        </td>
       <td colspan="4">
        	<table><?php echo $objASSET->generateBalanceSheetTable_withPreviousYear($ASSETData,0,2);?></table>
        </td>
    </tr>
    <!--<tr class="rowstyle"><td style="font-size:14px;border:1px dashed gray !important;"><b>Profit & Loss A/c (Rs.)</b></td><td style="text-align:right;font-size:14px;border:1px dashed gray !important;"><b><?php //echo number_format($ProfitnLoss,2);?></b></td><td style="text-align:right;font-size:14px;border:1px dashed gray !important;"></td><td style="font-size:14px;border:1px dashed gray !important;"></td><td style="text-align:right;font-size:14px;border:1px dashed gray !important;"></td><td style="text-align:right;font-size:14px;border:1px dashed gray !important;"></td></tr>-->
    <tr class="rowstyle">
    	<td style="font-size:14px;border:1px dashed gray !important;"><?php echo number_format($PreviousYearLIABILITYTotal, 2);?></td>
    	<td style="font-size:14px;border:1px dashed gray !important;"><b>TOTAL(Rs.)</b></td>
        <td style="text-align:right;font-size:14px;border:1px dashed gray !important;"><b><?php echo number_format($LIABILITYTotal, 2);?></b></td>
        <td style="text-align:right;font-size:14px;border:1px dashed gray !important;"></td>
        <td style="font-size:14px;border:1px dashed gray !important;"><?php echo number_format($PreviousAssetTotal, 2);?></td>
        <td style="font-size:14px;border:1px dashed gray !important;"><b>TOTAL(Rs.)</b></td>
        <td style="text-align:right;font-size:14px;border:1px dashed gray !important;"><b><?php echo number_format($ASSETTotal, 2);?></b></td>
        <td style="text-align:right;font-size:14px;border:1px dashed gray !important;"></td>
    </tr>
    <?php }
	else{ ?>
	 <tr class="rowstyle">
      <th style="text-align:left;font-size:14px;border:1px dashed gray !important; width:300px;" >Particulars(Liabilities)</th>
      <th style="text-align:center;font-size:14px;border:1px dashed gray !important;width:200px;"  colspan="2">Closing Balance(Rs.)</th>
      <th style="text-align:left;font-size:14px;border:1px dashed gray !important;width:300px;">Particulars(Assets)</th>
      <th style="text-align:center;font-size:14px;border:1px dashed gray !important;width:200px;" colspan="2">Closing Balance(Rs.)</th>
    </tr>
    <tr class="rowstyle">
    	<td colspan="3">
    		<table>
            	
				<?php 
					echo $objLIABILITY->generateBalanceSheetTable($LIABILITYData,0,1);
				?>
            	<tr>
                	<td style="border:1px solid black;"><b>Profit & Loss A/c (Rs.)</b></td>
                    <td style="border:1px solid black;"><b><?php echo number_format($ProfitnLoss,2);?></b></td>
                    <td style="border:1px solid black;"></td>
                </tr>
             </table>
        </td>
       <td colspan="3">
        	<table><?php echo $objASSET->generateBalanceSheetTable($ASSETData,0,2);?></table>
        </td>
    </tr>
    <!--<tr class="rowstyle"><td style="font-size:14px;border:1px dashed gray !important;"><b>Profit & Loss A/c (Rs.)</b></td><td style="text-align:right;font-size:14px;border:1px dashed gray !important;"><b><?php //echo number_format($ProfitnLoss,2);?></b></td><td style="text-align:right;font-size:14px;border:1px dashed gray !important;"></td><td style="font-size:14px;border:1px dashed gray !important;"></td><td style="text-align:right;font-size:14px;border:1px dashed gray !important;"></td><td style="text-align:right;font-size:14px;border:1px dashed gray !important;"></td></tr>-->
    <tr class="rowstyle">
    	<td style="font-size:14px;border:1px dashed gray !important;"><b>TOTAL(Rs.)</b></td>
        <td style="text-align:right;font-size:14px;border:1px dashed gray !important;"><b><?php echo number_format($LIABILITYTotal, 2);?></b></td>
        <td style="text-align:right;font-size:14px;border:1px dashed gray !important;"></td>
        <td style="font-size:14px;border:1px dashed gray !important;"><b>TOTAL(Rs.)</b></td>
        <td style="text-align:right;font-size:14px;border:1px dashed gray !important;"><b><?php echo number_format($ASSETTotal, 2);?></b></td>
        <td style="text-align:right;font-size:14px;border:1px dashed gray !important;"></td>
    </tr>
	<?php	}?>
</table>
</div>
</center>
</div>

<br />
<br />

</div>

<?php include_once "includes/foot.php"; ?>
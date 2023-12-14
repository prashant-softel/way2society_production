
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
<title>W2S - Generate Bill</title>
</head>




<?php //include_once "ses_set_s.php"; ?>
<?php include_once("includes/head_s.php");
include_once "check_default.php";
include_once("classes/include/fetch_data.php");
include_once("classes/dbconst.class.php");
include_once("classes/latestcount.class.php");
include_once("classes/unit.class.php");
//include_once ("classes/include/exportToExcel.php");
$obj_unit_class = new unit($m_dbConn);
if(isset($_GET['ssid'])){if($_GET['ssid']<>$_SESSION['society_id']){?><script>window.location.href = "logout.php";</script><?php }}


$objFetchData = new FetchData($dbConn);
$objFetchData->GetSocietyDetails($_SESSION["society_id"]);
// Lock buttons freez year 
$btnDisable = "";
if($_SESSION['is_year_freeze'] <> 0)
{
	$btnDisable = "disabled";
}
else
{
	$btnDisable = "";
}


// Check Ledger Round Of value is set or not

if($_SESSION['default_ledger_round_off'] == 0 && $objFetchData->objSocietyDetails->sLedgerRoundOffSet)
{
?>
<script>
	alert('Please first set Ledger Round Off in default setting');
	window.location.href = "defaults.php";
</script>
<?php }


include_once("classes/genbill.class.php");
$obj_genbill = new genbill($m_dbConn, $m_dbConnRoot);
$lblPeriodID = "";

include_once('classes/billDetailsValidation.class.php');
$obj_billValidate = new billValidation($m_dbConn);

if($_REQUEST['mode']=='Generate' || $_REQUEST['mode']=='View' || $_REQUEST['mode']=='Export To Excel')
{	
	$errorMsg = "";
}
else
{
	$errorMsg = $obj_billValidate->validateLedger();
	$errorMsg2 =  $obj_billValidate->validatePeriods();
	$errorMsg3 = $obj_billValidate->validateServiceTaxLedger();
}

$_SESSION['ssid'] = $_REQUEST['ssid'];
$_SESSION['wwid'] = $_REQUEST['wwid'];
?>
 
<html>
<head>
<style type="text/css">
  table.cruises td { 
    border-left: 1px solid #999; 
    border-top: 1px solid #999;  
    padding: 2px 4px;
    }
  table.cruises tr:first-child td {
    border-top: none;
  }
	 table.cruises td { background: #eee; overflow: hidden; }
  
  div.scrollableContainer { 
    position: relative; 
	width:100%;
    margin: 0px; 
	border: 1px solid #999;   
   }
  div.scrollingArea { 
    height: 500px; 
    overflow: auto;
  }
  table.cruises th { 
    border-left: 1px solid #999; 
    padding: 2px 4px;
    background: #6b6164;
    color: white;
    font-variant: small-caps;
    }
  </style>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<link href="css/messagebox.css" rel="stylesheet" type="text/css" />
    
	<script type="text/javascript" src="js/ajax_new.js"></script>
    <script type="text/javascript" src="js/populateData.js"></script>
	<script type="text/javascript" src="js/jsgenbill_20190326.js?15062023"></script>
    <script type="text/javascript" src="ckeditor/ckeditor.js"></script>
   <script type="text/javascript" src="js/status.js"></script> 
    <!--<link rel="stylesheet" href="css/ui.datepicker.css" type="text/css" media="screen" />
    <script type="text/javascript" src="javascript/jquery-1.2.6.pack.js"></script>
    <script type="text/javascript" src="javascript/jquery.clockpick.1.2.4.js"></script>
    <script type="text/javascript" src="javascript/ui.core.js"></script>
    <script type="text/javascript" src="javascript/ui.datepicker.js"></script>-->
    
	
	<script type="text/javascript" src="js/validate.js"></script>
     <script type="text/javascript">
        $(function()
        {
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker({ 
            dateFormat: "dd-mm-yy", 
            showOn: "both", 
            buttonImage: "images/calendar.gif", 
            buttonImageOnly: true,
			minDate: minGlobalCurrentYearStartDate
			//maxDate: maxGlobalCurrentYearEndDate
			//changeMonth: true,
			//changeYear: true,
			//showButtonPanel: true, closeText: 'Clear', 
        })});
		
		

    </script>
	<script language="javascript" type="application/javascript">
	
	function go_error()
    {
        setTimeout('hide_error()',50000);	
    }
	function ViewClick() 
	{
		var YearID = document.getElementById("year_id");
		var YearValue = YearID.options[YearID.selectedIndex].value;
		document.getElementById('txtYear_ID').value = YearValue;
		document.getElementById('txtYear_ID').innerHTML = YearValue;
		var PeriodID = document.getElementById("period_id");
		var PeriodVal = PeriodID.options[PeriodID.selectedIndex].value;
		document.getElementById('txtPeriod_ID').value = PeriodVal;
	}
    function hide_error()
    {
		document.getElementById('error').innerHTML = '';
        document.getElementById('error').style.display = 'none';	
    }
	function ViewPDF()
	{
		var sData = document.getElementById('maintenance_bill').innerHTML;
		
		var sHeader = '<html><head>';
		sHeader += '<style> table {	border-collapse: collapse; } table, th, td { border: 1px solid black; text-align: center; } </style>';	
		sHeader +=	'</head><body>';
		
		var sFooter =  '</body></html>';
		
		sData = sHeader + sData + sFooter;
		
		var sFileName = "testbill";
		
		var sURL = "viewpdf.php";
		var obj = {"data":sData, "file":sFileName};
		remoteCallNew(sURL, obj, 'queryResult');
	}
	
	function queryResult()
	{
		//alert("complete");
	}
	</script>
</head>

<?php if(isset($_REQUEST['ShowData']) || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])){ ?>
<body onLoad="go_error();">
<?php }else{ ?>
<body>
<?php } ?>

<br>
<div id="middle">
<div class="panel panel-info" id="panel" style="display:none">
        <div class="panel-heading" id="pageheader">Generate Bill</div>
        <div style="text-align:right;padding-right: 50px;padding-top: 10px;">
        <input type="button" id="insertView" value="Tarrif"  class="btn btn-primary"  style="width:100px;padding: 2px 7px;" onClick="window.open('billmaster.php', '_blank')" />
        </div>
        <div style="text-align:right;padding-right: 50px;padding-top: 10px;">
        <input type="button" id="insertView" value="Category"  class="btn btn-primary"  style="width:100px;padding: 2px 7px;" onClick="window.open('account_category.php', '_blank')" />
        </div>
        <div style="text-align:right;padding-right: 50px;padding-top: 10px;">
        <input type="button" id="insertView" value="Ledger"  class="btn btn-primary"  style="width:100px;padding: 2px 7px;" onClick="window.open('ledger.php', '_blank')" />
        </div>
        <?php if($_SESSION['society_id'] == 284){?>
        <div style="text-align:right;padding-right: 50px;padding-top: 10px;">
        <input type="button" id="gstNoThreshold" value="Set Flag"  class="btn btn-primary"  style="width:100px;padding: 2px 7px;" onClick="ShowGSTNoThresholdFlag()"  />
        </div>
        <?php }?>
<!--<center><div id="pageheader">Generate Bill</div>-->
<!--<center><font color="#43729F" size="+1"><b>Generate Bill</b></font></center>-->
<center>
<?php if(!isset($_REQUEST['ws'])){ $val ='';?>
<!--
<br>
<center>
<a href="society_view.php?imp" style="color:#00F; text-decoration:none;"><b>Add Unit</b></a>
</center>
-->

<?php } ?>

<div id="maintenance_bill">
<form name="genbill" id="genbill" method="post" action="process/genbill.process.php" <?php echo $val;?> onSubmit="return checkType();">
<input type="hidden" name="ssid" value="<?php echo $_GET['ssid'];?>">
<input type="hidden" name="wwid" value="<?php echo $_GET['wwid'];?>">
<input type="hidden" name="society_code" id="society_code" value="<?php echo $obj_fetch->objSocietyDetails->sSocietyCode;?>">
	<?php
		$star = "<font color='#FF0000'>*</font>";
		if(isset($_REQUEST['msg']))
		{
			$msg = "Sorry !!! You can't delete it. ( Dependency )";
		}
		else if(isset($_REQUEST['msg1']))
		{
			$msg = "Record Deleted Successfully.";
		}
		else
		{
			//$msg = '';	
		}
	?>

<script type="text/javascript">
            function showMessage() {
                alert("Send email to techsupport@gmail.com to change prev billing period ");
            }
        </script>
    <input type="hidden" name="society_id" id="society_id" value="<?php echo DEFAULT_SOCIETY; ?>" />
    <table align='center' style="margin-top:-100px;">
		<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $_REQUEST["ShowData"]; ?></b></font></td></tr>	        
        <tr align="left">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Wing</td>
            <td>&nbsp; : &nbsp;</td>
			<td>
                <select name="wing_id" id="wing_id" style="width:142px;" onChange="get_unit(this.value);">
                    <?php echo $combo_wing = $obj_genbill->combobox("select wing_id,wing from wing where status='Y' and society_id = '" . DEFAULT_SOCIETY . "'", $_REQUEST['wing_id'], 'All', '0'); ?>
				</select>
            </td>
		</tr>
        
		<tr align="left">
        	<td valign="middle"><?php if(isset($_GET['ws'])){echo $star;}?></td>
			<td>Unit No. ( Flat No )</td>
            <td>&nbsp; : &nbsp;</td>
			<td>
                <select name="unit_id" id="unit_id" style="width:142px;">
                	<?php echo $combo_unit = $obj_genbill->combobox("select u.unit_id, CONCAT(CONCAT(u.unit_no,' '), mm.owner_name) AS 'unit_no' from unit AS u JOIN `member_main` AS mm ON u.unit_id = mm.unit where u.status = 'Y' and u.society_id = '" . $_SESSION['society_id'] . "' and mm.ownership_status=1 ORDER BY u.sort_order ", $_REQUEST['unit_id'], "All", '0');
					?>
				</select>
            </td>
		</tr>
        
        <!--<tr align="left">
        	<td valign="middle"><?php //if(isset($_GET['ws'])){echo $star;}?></td>
			<td>Bill Year </td>
            <td>&nbsp; : &nbsp;</td>
			<td>
            	<select name="year_id" id="year_id" style="width:142px;" onChange="get_period(this.value, <?php echo DEFAULT_PERIOD; ?> );">
                	<?php //echo $combo_state = $obj_genbill->combobox("select YearID,YearDescription from year where status='Y' and YearID = '".$_SESSION['default_year']."' ORDER BY YearID DESC", DEFAULT_YEAR, '' ); ?>
				</select>
            </td>
		</tr> -->       
        <tr align="left">
        	<td valign="middle"><?php if(isset($_GET['ws'])){echo $star;}?></td>
			<td>Bill Year </td>
			<?php 
			?>
			<td>&nbsp; : &nbsp;</td>
			<td>
            <select name="year_id" id="year_id" style="width:142px;" onChange="get_period(this.value, <?php echo DEFAULT_PERIOD; ?> );">
                	<?php echo $combo_state = $obj_genbill->combobox("select YearID,YearDescription from year where status='Y' and YearID = '".$_SESSION['default_year']."' ORDER BY YearID DESC", DEFAULT_YEAR, '' ); ?>
				</select>
				&nbsp; &nbsp;
			    Bill Year of previous period :
			   <span  name="year_id1" id="year_id1" style="line-height: 20px;"></span>   

		</td>		
		</tr>        
        <tr align="left">
        	<td valign="middle"><?php if(isset($_GET['ws'])){echo $star;}?>
			<td>Bill For </td>
            <td>&nbsp; : &nbsp;</td>
			<td>
                <select name="period_id" id="period_id" style="width:142px;" onChange="get_date(this.value); get_prevperiod();">  		
			</select>
					&nbsp; &nbsp;
		         <!--Bill For previous period :	
				 <span name="prevperiod_id" id="prevperiod_id" style="line-height: 20px;"></span>   -->
			</td>
	
		</tr>
        
        <tr align="left">
        	<td valign="middle"><?php if(isset($_GET['ws'])){echo $star;}?></td>
			<td>Bill Date </td>
            <td>&nbsp; : &nbsp;</td>
			<td>
                <input type="text" name="bill_date" id="bill_date" style="width:142px;" readonly class="basics" />
            <input type="button" id="insertView" value="Change"  class="btn btn-primary"  style="width:100px; padding: 0px 0px; position: relative; left: 120px ;" onClick="showMessage()" />
		</td>
		</tr>
        <tr align="left" id="duedate_tr">
        	<td valign="middle"><?php if(isset($_GET['ws'])){echo $star;}?></td>
			<td>Due Date </td>
            <td>&nbsp; : &nbsp;</td>
			<td>
                <input type="text" name="due_date" id="due_date" style="width:142px;" readonly class="basics"/>
            </td>
		</tr>
		<tr align="left" id="duedatetoshow_tr">
        	<td valign="middle"><?php if(isset($_GET['ws'])){echo $star;}?></td>
			<td>Bill Display Due Date</td>
            <td>&nbsp; : &nbsp;</td>
			<td>
                <input type="text" name="due_date_to_display" id="due_date_to_display" style="width:142px;" readonly class="basics"/>
            </td>
		</tr>
        <script>
			get_date(document.getElementById('period_id').value);
		</script>
        <tr align="left">
        	<td valign="middle"><?php if(isset($_GET['ws'])){echo $star;}?></td>
			<td>Bill Start No </td>
            <td>&nbsp; : &nbsp;</td>
			<td>
            	<?php
					//$sqlBillNo = "select counter from counter where id=2";
					//$BillNo = $m_dbConn->select($sqlBillNo);					
					$obj_count = new latestCount($m_dbConn);
					$billno = $obj_count->getLatestBillNo($_SESSION['society_id'], false);
				?>
                <input type="text" name="bill_no" id="bill_no" style="width:142px;" value="<?php  echo $billno; ?>"/>
            </td>
		</tr>
        <!-- <tr align="left">
        	<td valign="middle"><?php //if(isset($_GET['ws'])){echo $star;}?></td>
			<td style="vertical-align:middle">Supplementary Bill</td>
            <td style="vertical-align:middle">&nbsp; : &nbsp;</td>
			<td>
                <input type="checkbox" name="gen_supplementary_bill" id="gen_supplementary_bill" style="width:14px;height:20px" value="" onChange="ToggleSupplementary_bill()" />
                <span style="color:#F00;vertical-align:middle" id="supplementary_bill_span">&nbsp;&nbsp;Enable it to generate supplementary bill instead of regular bill. </span>
            </td>
		</tr>-->
        
         <tr align="left">
        	<td valign="middle"></td>
			<td>Bill Type</td>
            <td>&nbsp; : &nbsp;</td>
			<td><?php $selVal = "selected";?>
            		<select name="bill_method" id="bill_method" value="<?php echo $_REQUEST['bill_method'];?>"   style="width:142px;" onChange="periodFetched()" >
            		<OPTION VALUE="<?php echo BILL_TYPE_REGULAR; ?>"  <?php echo $_REQUEST['bill_method'] == BILL_TYPE_REGULAR? $selVal:'';?>>Regular Bill</OPTION>
                    <OPTION VALUE="<?php echo BILL_TYPE_SUPPLEMENTARY; ?>"  <?php echo $_REQUEST['bill_method']  == BILL_TYPE_SUPPLEMENTARY? $selVal:'';?>>Supplementary Bill</OPTION>
                </select>
            </td>
		</tr>
        <tr align="left">
        	<td valign="middle"></td>
                 <td>Font Size (In pixel) </td>
                 <td>&nbsp; : &nbsp;</td>
                 <td> <input type="number" name="font_size" id="font_size"  style="width:80px;" /></td>
               </tr>
                <script>
		document.getElementById("font_size").value='12';
		//document.getElementById('font_size').value = '1000';
		</script> 
       <tr align="left">
        	<td valign="middle"><?php //if(isset($_GET['ws'])){echo $star;}?></td>
			<td style="vertical-align:middle">No Due Date</td>
            <td style="vertical-align:middle">&nbsp; : &nbsp;</td>
			<td><?php $chkVal = "checked";?>
                <input type="checkbox" name="hide_duedate" id="hide_duedate" style="width:14px;height:20px" value="1" onChange="periodFetched()" <?php echo (isset($_REQUEST['hide_duedate']) && $_REQUEST['hide_duedate'] ==1)? $chkVal:'';?> />
                <span style="color:#F00;vertical-align:middle" id="supplementary_bill_span">&nbsp;&nbsp;(Enable it for supplementary bill.)</span>
              </td>
		</tr>
       <tr align="left">
        	<td valign="middle"><?php //if(isset($_GET['ws'])){echo $star;}?></td>
			<td style="vertical-align:middle">No interest on prev bill amount</td>
            <td style="vertical-align:middle">&nbsp; : &nbsp;</td>
			<td><?php $chkVal = "checked";?>
                <input type="checkbox" name="no_int_on_prev_bill" id="no_int_on_prev_bill" style="width:14px;height:20px" value="1" <?php echo (isset($_REQUEST['no_int_on_prev_bill']) && $_REQUEST['no_int_on_prev_bill'] ==1)? $chkVal:'';?> />
                <span style="color:#F00;vertical-align:middle" id="supplementary_bill_span">&nbsp;&nbsp;(Interest would be charged on previous principal arrears.)</span>
              </td>
		</tr>
        
        <!--<tr align="left">
        	<td valign="middle"><?php //if(isset($_GET['ws'])){echo $star;}?></td>
			<td>Notes </td>
            <td>&nbsp; : &nbsp;</td>
			<td>
                <textarea name="bill_notes" id="bill_notes" rows="5" cols="50"></textarea>
            </td>
		</tr>-->
        </table>
        <br><br>
        <table>
        <tr><td colspan="4" style="text-align:center;">Bill Notes</td></tr>
        <tr><td colspan="4"><textarea name="bill_notes" id="bill_notes" rows="5" cols="50"></textarea></td></tr>
       	<script>
			CKEDITOR.config.height = 100;
			CKEDITOR.config.width = 500;
			CKEDITOR.replace('bill_notes', {toolbar: [
         						{ name: 'clipboard', items: ['Undo', 'Redo']},
        						{name: 'editing', items: ['Format', 'Bold', 'Italic', 'Underline', 'Strike'] }
   								 ]});
		</script>
        <tr><td colspan="4">&nbsp;</td></tr>
		
        <tr>
			<td colspan="4" align="center">
            <input type="hidden" name="mode" id="mode" value="" />
			<input type="button" id="insertView" value="View"  class="btn btn-primary"  style="width:100px;" onClick="SetMode(this.value);" />
            <?php if(IsReadonlyPage() == false)
             {

              if($_SESSION['profile'][PROFILE_GENERATE_BILL] == 1)
                { 
            ?>
			&nbsp;&nbsp;&nbsp;
            <input type="button" id="insertGenerate" value="Generate" style="width:92px;"  class="btn btn-primary" onClick="SetMode(this.value);" <?php echo $btnDisable; ?>/>
            <?php } 
			} ?>
			&nbsp;&nbsp;&nbsp;
            <input type="button" id="insertExport" value="Export To Excel" class="btn btn-primary" onClick="SetMode(this.value);" style="display:none;" />
            <?php if(IsReadonlyPage() == false && $_SESSION['profile'][PROFILE_GENERATE_BILL] == 1 ){?>
			&nbsp;&nbsp;&nbsp;
            <input type="button" id="insertNote" value="Update Notes" class="btn btn-primary" onClick="SetMode(this.value);" />
            <?php } ?>
            <?php if(IsReadonlyPage() == false && $_SESSION['profile'][PROFILE_GENERATE_BILL] == 1 ){?>
			&nbsp;&nbsp;&nbsp;
            <input type="button" id="insertFont" value="Update Font Size" class="btn btn-primary" onClick="SetMode(this.value);" />
               <?php } ?>
            </td>
		</tr>
</table>
<script>
	get_period('', '<?php echo DEFAULT_PERIOD; ?>','0');

	get_prevperiod('<?php echo DEFAULT_PERIOD; ?>');
	

</script>
       
</form>
<br/>
<div id="exportstatus" style="color:#0000FF;font-weight:bold;display:none;"></div>
<iframe id="download_bill" style="display:none"></iframe>
<?php 
$width=550;
if($_SESSION['profile'][PROFILE_GENERATE_BILL] == 0)
{
	$width=400;
}
?>
<table id="operations" style="display:none;width:<?php echo $width?>px;">
		<tr>
			<td>
				<input type="button" id="export" value="Generate PDF"  onclick="ExportPDF();" class="btn btn-primary" />
				&nbsp;&nbsp;
                                <input type="button" id="download" value="Download Bills"  onclick="DownloadBill();" class="btn btn-primary" />
                                &nbsp;
                                 <?php if($_SESSION['role'] == ROLE_SUPER_ADMIN)
                                {?>
                                <input type="button" id="deletebill" value="Delete All  Bills" onClick="billDelete('0','<?php echo $_REQUEST["period_id"]?>');" class="btn btn-primary" <?php echo $btnDisable; ?>/>
				&nbsp;
                                <?php }?>
                                <input type="button" id="notify" value="Send Notification"  onclick="window.location.href = 'notification.php';" class="btn btn-primary" />
			</td>
		</tr>
</table>
<br><br>	
<div id="GenPDFCnt"></div>
<table align="center">
<tr>
<td align="center"> 
<?php
echo "<br>";
if($_REQUEST['period_id'] <> "")
{
	//$dir='maintenance_bills/RHG_TEST/April-June 2019/';
	//$files1 = scandir($dir);
	//print_r($files1);
	$previousPDF_cnt = $getPDFCountPrevious =$obj_unit_class->GetPreviousPDFGen($_REQUEST['period_id'],$_REQUEST["bill_method"]);
	//print_r($previousPDF_cnt);
	?>
	<script>
	
	//alert("test");
		document.getElementById('GenPDFCnt').innerHTML='<span style="font-size: 12px;font-weight: 600;color: blue;"> PDF generated for '+<?php echo $previousPDF_cnt['pdfcnt']; ?>+' out of &nbsp;'+<?php echo $previousPDF_cnt['unitcnt']; ?>+' units  </spna>';
	</script> 
	<?php 
    $obj_genbill->SetReminderSMSDetails($_SESSION['society_id'], $_REQUEST['period_id']);
	$obj_genbill->SetReminderEmailDetails($_SESSION['society_id'], $_REQUEST['period_id']);
	$str1 = $obj_unit_class->pgnation_bill($_SESSION['society_id'], $_REQUEST['wing_id'], $_REQUEST['unit_id'], $_REQUEST['period_id'], IsReadonlyPage(),$_REQUEST["bill_method"]);
	//echo $str = $obj_unit_class->display1($str1, true);
	//echo "<br>";
	//$str1 = $obj_unit_class->pgnation_bill($_SESSION['society_id'], $_REQUEST['wing_id'], $_REQUEST['unit_id'], $_REQUEST['period_id']);
	echo "<br>";
	if($_REQUEST["bill_method"] == 1)
	{
		?>
		<script>
		//document.getElementById("gen_supplementary_bill").checked = true;
		//document.getElementById("supplementary_bill_span").innerHTML = "&nbsp;Supplementary bill(s) will be generated instead of regular bill&nbsp;&nbsp;&nbsp;";
			//document.getElementById("supplementary_bill_span").style.color = "#009900";
			//document.getElementById("gen_supplementary_bill").value = 1;
		</script>
        <?php
	}
	else
	{
		?>
        <script>
			//document.getElementById("gen_supplementary_bill").value = 0;
		</script>
        <?php
	}
	?>
	
  	<?php } ?>
    
<?php if(isset($_REQUEST['ShowData']) && $_REQUEST['ShowData'] == "Export")
{	
 ?>
    <script type="text/javascript"> 
		ExportExcel(<?php echo $_SESSION['society_id'] ?> , <?php echo $_REQUEST['wing_id'] ?>, <?php echo $_REQUEST['unit_id'] ?>, <?php echo $_REQUEST['period_id'] ?>, <?php echo $_REQUEST["bill_method"] ? 1 : 0 ?>);
	 </script>    		    	
<?php	
}
?>
<script>
	function ToggleSupplementary_bill()
	{
		/*if(document.getElementById("gen_supplementary_bill").checked == true)
		{
			document.getElementById("supplementary_bill_span").innerHTML = "&nbsp;Supplementary bill(s) will be generated instead of regular bill&nbsp;&nbsp;&nbsp;";
			document.getElementById("supplementary_bill_span").style.color = "#009900";
			document.getElementById("gen_supplementary_bill").value = 1;
		}
		else
		{
			document.getElementById("supplementary_bill_span").innerHTML = "&nbsp;Enable it to generate supplementary bill instead of regular bill(s)";
			document.getElementById("supplementary_bill_span").style.color = "#F00";
			document.getElementById("gen_supplementary_bill").value = 0;
		}
		var PeriodID = document.getElementById("period_id");
		var PeriodVal = PeriodID.options[PeriodID.selectedIndex].value;
		get_date(document.getElementById('period_id').value);*/
	}
	
		if(unitArray.length > 0)
		{
			document.getElementById('exportstatus').style.display = 'block';
			document.getElementById('operations').style.display = 'block';
			document.getElementById('insertExport').style.display = 'table-row';
			/*document.getElementById('notify').style.display = 'block';
			document.getElementById('download').style.display = 'block';*/
			
			for(var iUnitCnt = 0; iUnitCnt < unitArray.length; iUnitCnt++)
			{
				//document.getElementById('generateIFrames').innerHTML += '<iframe name="pdfexport_"' + unitArray[iUnitCnt] + ' id="pdfexport_"' + unitArray[iUnitCnt] + ' style="border:1px solid #0F0;width:100%;height:10px;"></iframe>';
				
			}
		}
	</script>
  	<?php //} ?>
 </td>
</tr>
</table>
<?php
if(isset($_REQUEST['setflag']))
{
	$showGSTNoThresholdReport = $obj_unit_class->ShowGSTNoThreshold();
 ?><br>
  	<input type="button" class = "btn btn-primary" value="Update GST NO Threshold Flag" onClick="UpdateGSTNoThresholdFlag()" />
<?php } 
?>
</div>
<br>
<?php
if($errorMsg <> "" || $errorMsg2 <> "" || $errorMsg3 <> "")
{
	echo $errorMsg;
	echo $errorMsg2;
	echo $errorMsg3;
?>
	<script>
		document.getElementById('maintenance_bill').style.display = 'none';
	</script>
<?php			
}
?>

<table align="center">
<tr> 
<td align="center">
</td>
</tr>
</table>

</center>
</div>

<!-- Custom dialog with Yes/No Button -->
<div id="openDialogYesNo" class="modalDialog">
	<div>
		<div id="message_yesno">
		</div>
	</div>
</div>
<div id="openDialogOk" class="modalDialog" >
	<div>
		<div id="message_ok">
		</div>
	</div>
</div>
<div id="openDialogdownloadBill" class="modalDialog" >
	<div>
		<div id="message_downloadbill">
		</div>
	</div>
</div>
</div>
  
<!--<iframe name="pdfexport" id="pdfexport" style="border:1px solid #0F0;width:100%;height:50px;"></iframe>-->
<?php include_once "includes/foot.php"; ?>

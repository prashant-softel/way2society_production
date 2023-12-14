<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>

	<title>W2S - Bill Master</title>
</head>




<?php include_once("includes/head_s.php");
include_once ("classes/dbconst.class.php"); 
include_once("classes/billmaster.class.php");
$obj_billmaster = new billmaster($m_dbConn);
include_once("classes/wing.class.php");
$obj_wing = new wing($m_dbConn);

$_SESSION['ssid'] = $_REQUEST['ssid'];
$_SESSION['wwid'] = $_REQUEST['wwid'];
//$btnDisable = "";
$btnDisable = "";
if($_SESSION['is_year_freeze'] == 1)
{
	$btnDisable = "disabled";
}
else
{
	$btnDisable = "";
}
?>
 
<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<link href="css/messagebox.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
	<script type="text/javascript" src="js/ajax_new.js"></script>
    <script type="text/javascript" src="js/populateData.js"></script>
	<script type="text/javascript" src="js/jsbillmaster.js"></script>
    <script type="text/javascript" src="js/validate.js"></script>
    <script language="javascript" type="application/javascript">
	
	function go_error()
    {
		hideLoader();
        setTimeout('hide_error()',3000);	
    }
	
    function hide_error()
    {
		document.getElementById('error').innerHTML = '';
        document.getElementById('error').style.display = 'none';	
    }
	
	//get_AccountHeader();
	
	function get_AccountHeader1()
	{
		<?php
			//print_r($obj_billmaster->fetch_acc_head());
			$headerList = json_decode($obj_billmaster->fetch_acc_head(), true);
			if($headerList <> '')
			{
				for($iHeaderCount = 1; $iHeaderCount < sizeof($headerList); $iHeaderCount++)
				{
				?>
					var obj = [];
					obj = {'id' : '<?php echo $headerList[$iHeaderCount]['id']; ?>', 'head' : '<?php echo $headerList[$iHeaderCount]['head']; ?>'};
					aryAccHead.push(obj);
				<?php
				}
			}
		?>
		
		//alert(aryAccHead.length);
	}
	</script>
    <style>
		input{box-shadow:none;}
	</style>
</head>

<?php if(isset($_REQUEST['ShowData']) || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])){ ?>
<body onLoad="go_error();">
<?php }else{ ?>
<body>
<?php } ?>

<br>
<div id="middle">
<div class="panel panel-info" id="panel" style="display:none">
        <div class="panel-heading" id="pageheader">Bill Master</div>
        <div style="text-align:right;padding-right: 50px;padding-top: 10px;">
        <input type="button" id="insertView" value="Ledger"  class="btn btn-primary"  style="width:100px;padding: 2px 7px;" onClick="window.open('ledger.php', '_blank')" />
        </div>
        <div style="text-align:right;padding-right: 50px;padding-top: 10px;">
        <input type="button" id="" value="Import Tariff"  class="btn btn-primary"  style="width:100px;padding: 2px 7px;" onClick="window.open('import_tariff.php?periodid=0', '_blank')" />
        </div>

<?php if(!isset($_REQUEST['ws'])){ $val ='';?>

<?php }else{ $val = 'onSubmit="return val();"';
?>
<br>
<center>
<a href="wing.php?imp&ssid=<?php echo $_REQUEST['ssid'];?>&s&idd=<?php echo time();?>" style="color:#00F; text-decoration:none;"><b>Back</b></a>
</center>
<?php } ?>

<center>
<form name="unit" id="unit" method="post" action="process/billmaster.process.php" <?php echo $val;?> onSubmit="return get_unit();">
<input type="hidden" name="ssid" value="<?php echo $_GET['ssid'];?>">
<input type="hidden" name="wwid" value="<?php echo $_GET['wwid'];?>">
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
    <table align='center' style="margin-top: -30px;">
		<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $_REQUEST["ShowData"]; ?></b></font></td></tr>	        
        <input type="hidden" name="society_id" id="society_id" value="<?php echo $_SESSION['society_id'];?>">
		<tr align="left">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Wing</td>
            <td>&nbsp; : &nbsp;</td>
			<td>
                <select name="wing_id" id="wing_id" style="width:142px;">
				</select>
            </td>
		</tr>
              
        <tr align="left">
        	<td valign="middle"><?php if(isset($_GET['ws'])){echo $star;}?></td>
			<td>Bill Year </td>
            <td>&nbsp; : &nbsp;</td>
			<td>
            	<select name="year_id" id="year_id" style="width:142px;" onChange="get_period(this.value, <?php echo DEFAULT_PERIOD; ?>, 'period_id');">
                	<?php echo $combo_state = $obj_billmaster->combobox("select YearID,YearDescription from year where status='Y' and YearID = '" . $_SESSION['default_year'] . "' ORDER BY YearID DESC", DEFAULT_YEAR,"",""); ?>
				</select>
            </td>
		</tr>        
        <tr align="left">
        	<td valign="middle"><?php if(isset($_GET['ws'])){echo $star;}?></td>
			<td>Bill For </td>
            <td>&nbsp; : &nbsp;</td>
			<td>
               <select name="period_id" id="period_id" style="width:142px;">
                	<?php echo $combo_state = $obj_billmaster->combobox("select ID, Type from period  where  status='Y' and YearID = '" . DEFAULT_YEAR . "'","0","Please Select"); ?>  
                </select>
            </td>
		</tr>
        
        
        <tr align="left"  <?php //if ($_SESSION['society_id'] <> '32' && $_SESSION['society_id'] == '59'){ echo 'style="visibility:hidden;"'; } ?>>
        	<td valign="middle"></td>
			<td>Bill Type</td>
            <td>&nbsp; : &nbsp;</td>
			<td><select name="bill_method" id="bill_method" value="<?php echo $_REQUEST['bill_method'];?>"  style="width:142px;" onChange="billTypeChange();">
            		<OPTION VALUE="<?php echo BILL_TYPE_REGULAR; ?>">Regular Bill</OPTION>
                    <OPTION VALUE="<?php echo BILL_TYPE_SUPPLEMENTARY; ?>">Supplementary Bill</OPTION>
                </select>
            </td>
		</tr>
        
		<!--<tr align="left" <?php //if ($_SESSION['society_id'] <> '32' && $_SESSION['society_id'] == '59'){ echo 'style="visibility:hidden;"'; } ?>>
        	<td valign="middle"><?php //if(isset($_GET['ws'])){echo $star;}?></td>
			<td>Supplementary Bill </td>
            <td>&nbsp; : &nbsp;</td>
			<td>
                <input type="checkbox" name="supplementary_bill" id="supplementary_bill" value="1" onChange="billTypeChange();"/>
            </td>
		</tr>-->
				
		<tr><td colspan="4">&nbsp;</td></tr>
				
        <tr>
			<td colspan="4" align="center">
            <?php if(isset($_GET['ws'])){?>
            
            <input type="hidden" name="id" id="id">
            <input type="submit" name="insert" id="insert" value="Insert">
            
            <?php }else{ ?>
            
            <input type="submit" name="insert" id="insert" value="Fetch Details"  style="color: #fff;background-color: #337ab7;border-color: #2e6da4;width:50%;margin-top:5%">
            
            <?php } ?>
            </td>
		</tr>
</table>
</form>
<script>get_wing();</script>
<br/><br/>
<div id="set_common" style="border:1px solid black; padding:5px; display:none;">
<div style="border-bottom:2px solid black;">
<b>Select Account Header and Amount to set to all Selected Units</b><br/><br/>
Account Header : <select id="header_combo"><?php //echo $obj_billmaster->combobox("select id, ledger_name from ledger where show_in_bill=1 and society_id = '" . $_SESSION['society_id'] . "'"); ?></select>
&nbsp;&nbsp;&nbsp;&nbsp;
Type : <select id="amt_type">
			<option value="1">Fixed Amount</option>
        	<option value="2">Split (Area Wise)</option>
        	<option value="3">Multiply (Area Wise)</option>
      </select>
&nbsp;&nbsp;&nbsp;&nbsp;
Amount : <input type="text" id="common_amt" />
<br/><br/>

<input type="button" value="Apply" onClick="ApplyAmt();" class="btn btn-primary" <?php echo $btnDisable;?>/>
<br/><br/>
</div>
<br/>
<b>Select Duration To Apply The Updated Tariff Amounts</b>
<br /><br />
<input type="radio" id="tariff_range_current" name="tariff_range" value="1" checked>&nbsp;&nbsp;Current Period 
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="radio" id="tariff_range_lifetime" name="tariff_range" value="0">&nbsp;&nbsp;Lifetime
<br/><br><input type="button" value="Update All" onClick="UpdateAllData(); "  class="btn btn-primary" <?php echo $btnDisable;?>>
<!--<b>Select Period Range To Apply The Updated Tariff Amounts</b>
<br/><br/>
<table  style="text-align:center;">
	<tr>
		<th>From :&nbsp;&nbsp;</th>
		<td>
			<select name="year_id_start" id="year_id_start" style="width:142px;" onChange="get_period(this.value, 0, 'period_id_start');">
                	<?php //echo $combo_state = $obj_billmaster->combobox("select YearID,YearDescription from year where status='Y' ORDER BY YearID DESC", DEFAULT_YEAR ); ?>
			</select>

        </td>
		<th>&nbsp;&nbsp;&nbsp;&nbsp;To :&nbsp;&nbsp;</th>
		<td>
			<select name="year_id_end" id="year_id_end" style="width:142px;" onChange="get_period(this.value, 0, 'period_id_end');">
                	<?php //echo $combo_state = $obj_billmaster->combobox("select YearID,YearDescription from year where status='Y' ORDER BY YearID DESC", 0, 'Lifetime' ); ?>
			</select>
		</td>
	</tr>
	<tr>
		<th></th>
		<td>
			<select name="period_id_start" id="period_id_start" style="width:142px;">
        	</select>
		</td>
		<th></th>
		<td>
			<select name="period_id_end" id="period_id_end" style="width:142px;">
        	</select>
		</td>
	</tr>
</table>-->
<br /><br />

</div>
<br /><br/>
<style type="text/css">
 
  /*table.cruises { 
    font-family: verdana, arial, helvetica, sans-serif;
    font-size: 11px;
    cellspacing: 0; 
    border-collapse: collapse; 
    width: 535px;    
    }*/
  table.cruises td { 
    border-left: 1px solid #999; 
    border-top: 1px solid #999;  
    padding: 2px 4px;
    }
  table.cruises tr:first-child td {
    border-top: none;
  }
  table.cruises th { 
    border-left: 1px solid #999; 
    padding: 2px 4px;
    background: #6b6164;
    color: white;
    font-variant: small-caps;
    }
  table.cruises td { background: #eee; overflow: hidden; }
  
  div.scrollableContainer { 
    position: relative; 
    padding-top: 6em;
	width:100%;
    margin: 0px; 
	border: 1px solid #999;   
   }
  div.scrollingArea { 
    height: 600px; 
    overflow: auto; 
    }

  table.scrollable thead tr {
    left: -1px; top: 0;
    position: absolute;
    }
</style>
<input type="hidden" id="freezyear" name="freeyear" value="<?php  echo $_SESSION['is_year_freeze'] ?>">
<div class = "scrollableContainer" style="display:none" id="scrollableDiv"> 
<div id="unit_info" style="text-align:center;" class="scrollingArea"></div>

<?php if(isset($_SESSION['admin'])){?>
<center>
<a href="unit_print.php?ssid=<?php echo $_GET['ssid'];?>&wwid=<?php echo $_GET['wwid'];?>&society_id=<?php if($_GET['society_id']<>""){echo $_GET['society_id'];}else{echo $_SESSION['society_id'];}?>&wing_id=<?php echo $_GET['wing_id'];?>&unit_no=<?php echo $_GET['unit_no'];?>&insert=<?php if($_GET['insert']<>""){echo $_GET['insert'];}else{echo 'Search';}?>&ShowData=&imp=" target="_blank"><img src="images/print.png" width="40" width="40" /></a>
</center>
<?php } ?>

<table align="center">
<tr>
<td align="center">
<?php
/*echo "<br>";
$str1 = $obj_unit->pgnation();
echo "<br>";
echo $str = $obj_unit->display1($str1);
echo "<br>";
$str1 = $obj_unit->pgnation();
echo "<br>";*/
?>
</td>
</tr>
</table>

</center>
</div>
</div>
<div id="detailDialog" class="modalDialog">
	<div>
		<div id="message_ok">
		</div>
	</div>
</div>
<script>
	get_period(document.getElementById('year_id').value, "<?php echo $obj_billmaster->getCurrentPeriod(); ?>", 'period_id');
	get_AccountHeader(0);
	
	//get_period(document.getElementById('year_id_start').value, "<?php //echo $obj_billmaster->getCurrentPeriod(); ?>", 'period_id_start');
	//get_period(document.getElementById('year_id_end').value, "<?php //echo $obj_billmaster->getCurrentPeriod(); ?>", 'period_id_end');
</script>
<?php include_once "includes/foot.php"; ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Bank Details</title>
</head>

<?php include_once "ses_set_as.php"; ?>
<?php
if(isset($_SESSION['admin']))
{
	include_once("includes/header.php");
}
else
{
	include_once("includes/head_s.php");
}
include_once("classes/dbconst.class.php");
include_once("classes/BankDetails.class.php");
include_once("classes/include/fetch_data.php");
$obj_BankDetails = new BankDetails($m_dbConn);
$objFetchDetails = new FetchData($m_dbConn);
$startdate = $obj_BankDetails->FetchDate($_SESSION['default_year']); 
?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
    <script type="text/javascript" src="js/ajax.js"></script>
   	<script type="text/javascript" src="js/ajax_new.js"></script>
	<script type="text/javascript" src="js/jsBankDetails.js"></script>
    
	<!--<link rel="stylesheet" href="css/ui.datepicker.css" type="text/css" media="screen" />
	<script type="text/javascript" src="js/jquery_min.js"></script>
	<script type="text/javascript" src="javascript/jquery-1.2.6.pack.js"></script>
    <script type="text/javascript" src="javascript/jquery.clockpick.1.2.4.js"></script>
    <script type="text/javascript" src="javascript/ui.core.js"></script>
    <script type="text/javascript" src="javascript/ui.datepicker.js"></script>-->
    <script language="javascript" type="application/javascript">
	$(function()
        {
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker({ 
            dateFormat: "dd-mm-yy", 
			defaultDate: new Date(),
            showOn: "both", 
            buttonImage: "images/calendar.gif", 
            buttonImageOnly: true 
        })});
	function go_error()
    {
		//$(document).ready(function()
		//{
			//$("#error").show();
		//});
		document.getElementById('error').style.display = 'block';
        setTimeout('hide_error()',8000);	
    }
    function hide_error()
    {
		$(document).ready(function()
		{
			$("#error").fadeOut("slow");
		});
    }
	function VerifyRequiredFields()
	{
		if(document.getElementById('AllowNEFT').checked == true)
		{
			if(document.getElementById('AcNumber').value == "") 
			{
				alert("A/C Number is mandatory for NEFT transactions.");
				document.getElementById('AcNumber').focus();
				document.getElementById('AllowNEFT').checked = false;
				return false;
			}
			else if (document.getElementById('IFSC_Code').value == "")
			{
				alert("IFSC Code is mandatory for NEFT transactions.");
				document.getElementById('IFSC_Code').focus();
				document.getElementById('AllowNEFT').checked = false;
				return false;
			}
		}
		else if(document.getElementById('BankName').value == "")
		{
			alert("Bank Name is mandatory for Bank.");
			document.getElementById('BankName').focus();
			return false;	
		}
		return true;
	}
	</script>
</head>

<?php if(isset($_REQUEST['ShowData']) || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])){ ?>
<body onLoad="go_error();">
<?php }else{ ?>
<?php } ?>
<body>
<br>
<div id="middle">

<div class="panel panel-info" id="panel" style="display:none">
        <div class="panel-heading" id="pageheader">Bank Details</div>

<?php
$star = "<font color='#FF0000'>*</font>";
if(isset($_REQUEST['msg']))
{
	$msg = "Sorry !!! You can't delete it. ( Dependency )";
}
else if(isset($_REQUEST['msg1']))
{
	$msg = "Deleted Successfully.";
}
else{}
?>
<center>
<form name="BankDetails" id="BankDetails" method="post" onSubmit="return VerifyRequiredFields();" action="process/BankDetails.process.php">
<table align='center'>
<?php
if(isset($msg))
{
	if(isset($_POST['ShowData']))
	{
?>
		<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $_POST['ShowData']; ?></b></font></td></tr>
<?php
	}
	else
	{
	?>
		<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $msg; ?></b></font></td></tr>
	<?php
	}
}
else
{
?>
		<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $_POST['ShowData']; ?></b></font></td></tr>
<?php
}
?>

		
        <tr align="left">
        	<td><?php echo $star;?>Ledger</td>
			<td>
                <select name="LedgerID" id="LedgerID" style="width:142px;" onChange="ledgerChange(this)">
                	<option value="0">Create New</option>
                    <?php echo $ledgercombo = $obj_BankDetails->combobox("SELECT leg.id, leg.ledger_name FROM ledger leg LEFT OUTER JOIN bank_master bank ON leg.id = bank.BankID WHERE  bank.BankID IS NULL and leg.categoryid IN('" . BANK_ACCOUNT . "', '".CASH_ACCOUNT."') and leg.society_id=".$_SESSION['society_id']); ?>
				</select>
            </td>
		</tr>
        
		<tr>
			<td><?php echo $star;?>Bank Name</td>
			<td><input type="text" name="BankName" id="BankName" /></td>
		</tr>
        
        <tr>
			<td>Bank Prefix</td>
			<td><input type="text" name="Bank_PreFix" id="Bank_PreFix" /></td>
		</tr>
		
        
        <tr>
			<td><?php //echo $star;?>Branch Name</td>
			<td><input type="text" name="BranchName" id="BranchName" /></td>
		</tr>
        
        <tr align="left">
        	<td><?php echo $star;?>Select Bank/Cash</td>
			<td>
                <select name="accountCategory" id="accountCategory" style="width:142px;">
                	<option value="<?php echo BANK_ACCOUNT ?>">Bank Account</option>
                    <option value="<?php echo CASH_ACCOUNT ?>">Cash Account</option>
                    
				</select>
            </td>
		</tr>
		
        
        <!--<tr>
			<td><?php //echo $star;?>Ledger Name</td>
			<td><input type="text" name="LedgerName" id="LedgerName" /></td>
		</tr>-->
        
        <tr>
			<td><?php //echo $star;?>Opening Balance</td>
			<td><input type="text" name="Balance" id="Balance" /></td>
		</tr>
        
        <tr>
			<td><?php //echo $star;?><!--Date--></td>
			<td><input type="hidden" name="Balance_Date" value="<?php if($_REQUEST['Balance_Date'] <> ""){echo $_REQUEST['Balance_Date'];}else{echo $startdate; }?>" id="Balance_Date"  readonly /></td>
		</tr>
        
        <tr>
        		<td><?php //echo $star;?>A/c Number</td>
                <td><input type="text" name="AcNumber" id="AcNumber"/></td>
               
        </tr>
        
        <tr>
        		<td><?php //echo $star;?>IFSC Code</td>
                <td><input type="text" name="IFSC_Code" id="IFSC_Code"/></td> 
        </tr>
        
                
		<tr>
			<td  style="padding:10px;padding-left:0px"><?php //echo $star;?>Accept NEFT Transactions</td>
			<td style="padding:10px"><input type="checkbox" name="AllowNEFT" id="AllowNEFT" value="1" onChange="VerifyRequiredFields()"/ ></td>
		</tr>
         <tr>
        		<td><?php //echo $star;?>MICR Code</td>
                <td><input type="text" name="MICR_Code" id="MICR_Code"/></td> 
        </tr>
        <tr>
			<td><?php //echo $star;?>Bank Address</td>
			<td><textarea name="Address" id="Address" rows="5" cols="32"></textarea></td>
		</tr>
        
		<tr>
			<td><?php //echo $star;?>Phone1</td>
			<td><input type="text" name="Phone1" id="Phone1" /></td>
		</tr>
		<tr>
			<td>Phone2</td>
			<td><input type="text" name="Phone2" id="Phone2" /></td>
		</tr>
		<tr>
			<td><?php //echo $star;?>Fax</td>
			<td><input type="text" name="Fax" id="Fax" /></td>
		</tr>
		<tr>
			<td><?php //echo $star;?>Email</td>
			<td><input type="text" name="Email" id="Email" /></td>
		</tr>
		<tr>
			<td><?php //echo $star;?>Website</td>
			<td><input type="text" name="Website" id="Website" /></td>
		</tr>
		<tr>
			<td><?php //echo $star;?>Contact Person</td>
			<td><input type="text" name="ContactPerson" id="ContactPerson" /></td>
		</tr>
		<tr>
			<td><?php //echo $star;?>Contact Person's Phone</td>
			<td><input type="text" name="ContactPersonPhone" id="ContactPersonPhone" /></td>
        
		</tr>
        
        <tr>
			<td><?php //echo $star;?>Note</td>
			<td><textarea name="Note" id="Note"  rows="5" cols="32"></textarea></td>
		</tr>
        
		<tr>
			<td colspan="2" align="center"><input type="hidden" name="id" id="id">
            <?php if($_SESSION['is_year_freeze'] == 0 )
			{?>
            	<input type="submit" name="insert" id="insert" value="Insert" style="color: #fff;background-color: #337ab7;border-color: #2e6da4;width:30%;height:20%;margin-top:5%">
            <?php  
			}
			else
			{?>
				<input type="submit" name="insert" id="insert" value="Insert" style="color: #fff;background-color: #337ab7;border-color: #2e6da4;width:30%;height:20%;margin-top:5%" disabled="disabled">
			<?php 
			} ?>  
                </td>
		</tr>
</table>
</form>


<table align="center">
<tr>
<td>
<?php
echo "<br>";
$str1 = $obj_BankDetails->pgnation();
/*echo "<br>";
echo $str = $obj_BankDetails->display1($str1);
echo "<br>";
$str1 = $obj_BankDetails->pgnation();
echo "<br>";*/
?>
</td>
</tr>
</table>
<center>
</div>
</div>
<?php include_once "includes/foot.php"; ?>
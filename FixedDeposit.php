
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
<title>W2S - Fixed Deposit </title>
</head>




<?php 
include_once("includes/head_s.php");
include_once("classes/dbconst.class.php");
include_once("classes/FixedDeposit.class.php");
$obj_FixedDeposit = new FixedDeposit($m_dbConn);
?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
    <link href="css/messagebox.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/ajax.js"></script>
   	<script type="text/javascript" src="js/ajax_new.js"></script>
	<script type="text/javascript" src="js/jsFixedDeposit.js?123"></script>
    <script type="text/javascript" src="js/jsViewLedgerDetails.js"></script>
    <script type="text/javascript" src="js/validate.js"></script>
      <script type="text/javascript" src="js/populateData.js"></script>
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
	 
	 
	 $(function()
        {
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics2").datepicker({ 
            dateFormat: "dd-mm-yy", 
			defaultDate: new Date(),
            showOn: "both", 
            buttonImage: "images/calendar.gif", 
            buttonImageOnly: true ,
			yearRange: "-10:+0",
			maxDate: '0'
        })});
		
	function go_error()
    {
		document.getElementById('error').style.display = 'block';
        setTimeout('hide_error()',5000);	
    }
    function hide_error()
    {
		$(document).ready(function()
		{
			$("#error").fadeOut("slow");
		});
    }
	</script>
   <style>
   .fddiv .fdform  tr
   {
	  line-height: 26px; 
	}
   </style> 
    
    
</head>

<?php if(isset($_REQUEST['ShowData']) || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])){ ?>
<body onLoad="go_error();">
<?php }else{ ?>
<?php } ?>
<body>
<br>
<div id="middle">

<div class="panel panel-info" id="panel" style="display:none">
        <div class="panel-heading" id="pageheader">Manage Fixed Deposits</div>
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
<center><br>
<button type="button" class="btn btn-primary btn-circle" onClick="history.go(-1);" style="float:left;margin-bottom: 10px;"  id="btnBack"><i class="fa  fa-arrow-left"></i></button>
<button type="button" class="btn btn-primary" onClick="window.location.href='FixedDepositReport.php'" id="btnViewReport" style="margin-bottom: 10px;">View Report</button>
<?php if($_SESSION['is_year_freeze'] == 0)
{?>
	<button type="button" class="btn btn-primary" onClick="addNew();" id="btnAdd" style="margin-bottom: 10px;">Create New FD</button>
 <?php 
 }?>
</center>
<center>
<div id="renew_header"></div>
<div id="new_entry" style="display:none; width:100%; " class="fddiv">
<form name="FixedDeposit" id="FixedDeposit" method="post" action="process/FixedDeposit.process.php?module=2" onSubmit="return validateData2();" class="fdform">
<input type="hidden" name="form_error"  id="form_error" value="<?php echo $_REQUEST["form_error"]; ?>" />

<table align='center' width="100%">
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
		
       <tr >
        	<td width="25%"><?php echo $star;?>FD Created In Bank Name :</td>
			<td width="25%">
                <select name="FD_Bank_Name" id="FD_Bank_Name"  value="<?php echo $_REQUEST['FD_Bank_Name'];?>">
                	 <?php echo $fd_bank = $obj_FixedDeposit->combobox("select `id`, ledgertable.ledger_name from `ledger` as ledgertable Join `account_category` as categorytbl on  categorytbl.category_id=ledgertable.categoryid where  ledgertable.categoryid = '".BANK_ACCOUNT."' and society_id = '".$_SESSION['society_id']."' ",$_REQUEST['FD_Bank_Name'],'Please Select',0); ?>
				</select>
            </td>
            <td width="25%"><?php echo $star;?>Category :</td>
			<td width="25%">
                <select name="Category" id="Category"   value="<?php echo $_REQUEST['Category'];?>">
                	 <?php echo $Category = $obj_FixedDeposit->combobox("SELECT  `category_id` ,`category_name` FROM `account_category`  where `is_fd_category`= '1' ",$_REQUEST['Category'],'Please Select',0); ?>
				</select>
            </td>
		</tr>
        
        <!--<tr >
        	<td><?php //echo $star;?>Category :</td>
			<td>
                <select name="Category" id="Category"   value="<?php //echo $_REQUEST['Category'];?>">
                	 <?php //echo $Category = $obj_FixedDeposit->combobox("SELECT  `category_id` ,`category_name` FROM `account_category`  where `is_fd_category`= '1' ",$_REQUEST['Category'],'Please Select',0); ?>
				</select>
            </td>
		</tr>-->
        
        <tr align="left">
        	<td><?php echo $star;?>FD Name :</td>
			<td><input type="text" name="FD_Name" id="FD_Name" value="<?php echo $_REQUEST['FD_Name'];?>" onKeyPress="return blockSpecialChars(event);"/></td> 
            <td><?php echo $star;?>FDR No :</td>
                <td><input type="text" name="FDR_No" id="FDR_No" value="<?php echo $_REQUEST['FDR_No'];?>"  onkeypress="return blockSpecialChars(event);"/></td> 
        </tr>
        
        <!--<tr>
        		<td><?php //echo $star;?>FDR No :</td>
                <td><input type="text" name="FDR_No" id="FDR_No" value="<?php //echo $_REQUEST['FDR_No'];?>"  onkeypress="return blockSpecialChars(event);"/></td> 
        </tr>-->
		
         <tr>
			<td><?php echo $star;?>Date of Deposit :</td>
			<td><input type="text" name="Deposit_Date" id="Deposit_Date" class="basics2"  onChange="ValidateDate(this.id,true);CheckYearValidation();"    value="<?php echo $_REQUEST['Deposit_Date'];?>"   /></td>
            <td><?php echo $star;?>Date of Maturity :</td>
			<td><input type="text" name="Maturity_Date" id="Maturity_Date" class="basics" onChange="ValidateDate(this.id,false);"  value="<?php echo $_REQUEST['Maturity_Date'];?>"    /></td>
		</tr>
        
         <!--<tr>
			<td><?php //echo $star;?>Date of Maturity :</td>
			<td><input type="text" name="Maturity_Date" id="Maturity_Date" class="basics" onChange="ValidateDate(this.id,false);"  value="<?php //echo $_REQUEST['Maturity_Date'];?>"    /></td>
		</tr>-->
        
        <!--<tr>
			<td>FD Period:</td>
			<td><input type="text" name="FD_Period" id="FD_Period"  value="<?php echo $_REQUEST['FD_Period'];?>"/></td>
		</tr>-->
        
        <tr>
        		<td><?php echo $star;?>Principal Amount (Rs.) :</td>
                <td><input type="text" name="Principal_Amount" id="Principal_Amount" value="<?php echo $_REQUEST['Principal_Amount'];?>"  onBlur="extractNumber(this,2,false);" onKeyUp="extractNumber(this,2,false);" onKeyPress="return blockNonNumbers(this, event, true, false);"/></td> 
                <td><?php echo $star;?>Maturity Amount (Rs.) :</td>
                <td><input type="text" name="Maturity_Amount" id="Maturity_Amount" value="<?php echo $_REQUEST['Maturity_Amount'];?>"  onBlur="extractNumber(this,2,false);" onKeyUp="extractNumber(this,2,false);" onKeyPress="return blockNonNumbers(this, event, true, false);"/></td> 
        </tr>
            
		<tr>
			<td><?php echo $star;?>Rate of Interest (%)( p.a.) :</td>
			<td><input type="text" name="Interest_Rate" id="Interest_Rate" value="<?php echo $_REQUEST['Interest_Rate'];?>"   onBlur="extractNumber(this,2,false);" onKeyUp="extractNumber(this,2,false);" onKeyPress="return blockNonNumbers(this, event, true, false);return blockSpecialChars(event);"/></td>
            <td>FD Period (in months):</td>
			<td><input type="text" name="FD_Period" id="FD_Period"  value="<?php echo $_REQUEST['FD_Period'];?>"/></td>
		</tr>
            
		<!--<tr>
        		<td><?php //echo $star;?>Maturity Amount (Rs.) :</td>
                <td><input type="text" name="Maturity_Amount" id="Maturity_Amount" value="<?php //echo $_REQUEST['Maturity_Amount'];?>"  onBlur="extractNumber(this,2,false);" onKeyUp="extractNumber(this,2,false);" onKeyPress="return blockNonNumbers(this, event, true, false);"/></td> 
        </tr>-->
      
        
        <tr align="left">
        	<td colspan="2"><?php echo $star;?>Note :</td>
			<td colspan="2" style="font-size:14px;"><textarea id="Note" name="Note" style="width:200px; height:60px;border: 1px solid;"  maxlength="1000" ><?php echo $_REQUEST['Note'];?></textarea></td>
        </tr>
        
    	<tr  class="fd_options">
            <td colspan="2">
                <table style="width:53%;" align="center" >
                    <tr>
                    	<!--<td>Renew FD A/c : </td><td><input type="checkbox" name="FD_Renew" id="FD_Renew" value="1"  disabled  style="margin-top: 1px;" onClick="validateRenew();"  <?php //echo $_REQUEST['FD_Renew'] == '1' ? checked : '';?>/>&nbsp;&nbsp;&nbsp;</td>-->
                         <td style="display:none">Close FD A/c : </td><td><input type="checkbox" name="FD_Close" id="FD_Close" value="1" disabled   style="margin-top: 7px; display:none"onClick="validateClose();"  <?php //echo $_REQUEST['FD_Close'] == '1' ? checked : ''; ?>/></td>
                    </tr>
               </table>
            </td>
        </tr>
		
        <!-- <tr   class = "closefd" style=" display:none;">
        	<td><?php //echo $star;?>Deposit Maturity Amount To Bank  :</td>
			<td>
                <select name="Bank_Name" id="Bank_Name"  >
                	 <?php //echo $fd_purpose = $obj_FixedDeposit->combobox("select `id`,concat_ws(' - ', ledgertable.ledger_name,ledgertable.id)  from `ledger` as ledgertable Join `account_category` as categorytbl on  categorytbl.category_id=ledgertable.categoryid where  ledgertable.categoryid = '".BANK_ACCOUNT."' and society_id = '".$_SESSION['society_id']."' ",$_REQUEST['Bank_Name'],'Please Select',0); ?>
				</select>
            </td>
		</tr>
        
        <tr class = "closefd" style=" display:none;" >
       		<td><?php //echo $star;?>Cheque Number  :</td>
        	<td>
            	<input type='text' id='ChequeNumber' name='ChequeNumber' onBlur='extractNumber(this,0,false);' onKeyUp='extractNumber(this,0,false);' onKeyPress='return blockNonNumbers(this, event, false, false);' style='width:80px;'  value="<?php echo $_REQUEST['ChequeNumber'];?>"/>
             </td>
        </tr>-->
        
      
         <tr><td colspan="2" ><BR/></td></tr>
   </table>
  <table style="width:145%; display:none !important;" id="int_table">
   		<tr align="center" height="30px">
               <th style="width:40%;text-align:center;background-color:#337ab7; font-size:14px;color: #fff;padding-top: 5px;">Interest Type</th>     	
               <th style="width:40%;text-align:center;background-color:#337ab7; font-size:14px;color: #fff;padding-top: 5px;">Ledger Name</th>
               <th style="width:20%;text-align:center;background-color:#337ab7;font-size:14px;color: #fff;padding-top: 5px;">Amount  (Rs.)</th>
         </tr>
        <tr >
                <td style="width:40%;text-align:left;"><?php echo $star;?>Accrued Interest on FD  (Rs.)</td>
                <td>
                    <select name="accrued_interest_legder" id="accrued_interest_legder"  value="<?php echo $_REQUEST['accrued_interest_legder'];?>" >
                         <?php echo $accrued_interest_legder = $obj_FixedDeposit->combobox("select `id`,concat_ws(' - ', ledgertable.ledger_name,ledgertable.id)  from `ledger` as ledgertable Join `account_category` as categorytbl on  categorytbl.category_id=ledgertable.categoryid where  categorytbl.group_id = '".ASSET."' and ledgertable.categoryid <> '".DUE_FROM_MEMBERS."'and society_id = '".$_SESSION['society_id']."' ORDER BY ledgertable.ledger_name",$_REQUEST['accrued_interest_legder'],'Please Select',0); ?>
                    </select>
                </td>
                <td><input type="text" name="accrued_interest_amt" id="accrued_interest_amt" value="<?php echo $_REQUEST['accrued_interest_amt'];?>"  onBlur="extractNumber(this,2,false);" onKeyUp="extractNumber(this,2,false);" onKeyPress="return blockNonNumbers(this, event, true, false);"/></td> 
        </tr>
        <tr >
                <td style="width:40%; text-align:left;"><?php echo $star;?>Interest on FD   (Rs.)</td>
                <td>
                    <select name="interest_legder" id="interest_legder"  value="<?php echo $_REQUEST['interest_legder'];?>" >
                         <?php echo $interest_legder = $obj_FixedDeposit->combobox("select `id`,concat_ws(' - ', ledgertable.ledger_name,categorytbl.category_name)  from `ledger` as ledgertable Join `account_category` as categorytbl on  categorytbl.category_id=ledgertable.categoryid where categorytbl.group_id = '".LIABILITY."' or (categorytbl.group_id = 2 and categorytbl.is_fd_category = 1) ORDER BY ledgertable.ledger_name",$_REQUEST['interest_legder'],'Please Select',0);
						
						  ?>
                    </select>
                </td>
                <td><input type="text" name="interest_amt" id="interest_amt" value="<?php echo $_REQUEST['interest_amt'];?>"  onBlur="extractNumber(this,2,false);" onKeyUp="extractNumber(this,2,false);" onKeyPress="return blockNonNumbers(this, event, true, false);" /></td> 
        </tr>
         <tr><td colspan="3" ><BR/></td></tr>
         <tr>
			<td><?php echo $star;?>Date of Interest Paid/Accured:</td>
			<td><input type="text" name="Interest_Date" id="Interest_Date" class="basics2" onChange="ValidateDate(this.id,true);"    value="<?php echo $_REQUEST['Interest_Date'];?>"    /></td>
		</tr>
                    <tr>
                    	<!--<td>Renew FD A/c : </td><td><input type="checkbox" name="FD_Renew" id="FD_Renew" value="1"  disabled  style="margin-top: 1px;" onClick="validateRenew();"  <?php //echo $_REQUEST['FD_Renew'] == '1' ? checked : '';?>/>&nbsp;&nbsp;&nbsp;</td>-->
                         <td>Payout to Bank A/c : </td><td><input type="checkbox" name="FD_Bank_Payout" id="FD_Bank_Payout" value="1"    style="margin-top: 7px;"onClick="validateClose();"  <?php //echo $_REQUEST['FD_Close'] == '1' ? checked : ''; ?>/></td>
                    </tr>
        <tr align="left">
        	<td><?php echo $star;?>Interest Description :</td>
			<td colspan="5" style="font-size:14px;"><textarea id="Interest_Note" name="Interest_Note" style="width:200px; height:60px;border: 1px solid;"  maxlength="1000" ><?php echo $_REQUEST['Interest_Note'];?></textarea></td>
        </tr>
        
  </table>
   <table>      
        <tr>
            <td colspan="2" align="center">
                <input type="hidden" name="id" id="id"   value="<?php echo $_REQUEST['id'];?>" />
                <input type="hidden" name="mode" id="mode"   value="<?php echo $_REQUEST['mode'];?>" />
                <input type="hidden" name="ref" id="ref"    />
                <input type="hidden" name="fd_readonly" id="fd_readonly"   value="<?php echo $_REQUEST["fdreadonly"]; ?>" />
                <center><input type="submit" class="btn btn-primary" name="insert" id="insert" value="Insert"  style="padding: 6px 12px; color:#fff;background-color: #2e6da4;"/>
               <input type="button" name="cancel" class="btn btn-primary"  id="cancel" value="Cancel" onClick="window.location.href='FixedDeposit.php'" style="padding: 6px 12px; color:#fff;" />
            	</center>
            </td>
        </tr>
</table>

</form>
</div>
<div id="status" style="display:none;">
<div id="status_msg"></div></div>

<table align="center">
<tr>
<div class="" >
<ul class="nav nav-tabs">

<li class="active"><a href="#" onClick="fetchFDTable(1);" data-toggle="tab">All</a></li>
<li><a href="#"  onClick="fetchFDTable(2);" data-toggle="tab">Pending</a></li>
<li><a href="#"  onClick="fetchFDTable(3);" data-toggle="tab">Active</a></li>
<li><a href="#"  onClick="fetchFDTable(4);" data-toggle="tab" >Renewed</a></li>
<li><a href="#"  onClick="fetchFDTable(5);" data-toggle="tab">Closed</a></li>
</ul>
</div>

</tr>
<tr>
<td align="center">
<?php 
echo "<br>";
?>
<div id='showTable' style="font-weight:lighter;"></div>
<?php 
echo "<br>";
?>
</td>
</tr>
</table>
</center>
</div>
</div>
<!-- Custom dialog with Yes/No Button -->
<div id="openDialogYesNo" class="modalDialog">
	<div>
		<div id="message_yesno">
		</div>
	</div>
</div>

<script>
if(document.getElementById('form_error').value == '1')
{
	formError();
}
</script>
<?php
if(isset($_GET['edt']) && $_GET['edt'] <> '')
{
?>
<script>
	getDetails('edit-' + <?php echo  $_GET['edt'] ?>); 
</script>
<?php    
}?>
<?php include_once "includes/foot.php"; ?>

<div id="openDialogOk" class="modalDialog" >
	<div style="margin:2% auto; ">
		<div id="message_ok">
		</div>
	</div>
</div>

<script>
	fetchFDTable(1);
</script>
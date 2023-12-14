<?php 
include_once("includes/head_s.php");
include_once("classes/dbconst.class.php");
include_once("classes/FixedDeposit.class.php");
$obj_FixedDeposit = new FixedDeposit($m_dbConn);
$data = $obj_FixedDeposit->get_details_for_renew($_REQUEST['edt']);

?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
    <link href="css/messagebox.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/ajax.js"></script>
   	<script type="text/javascript" src="js/ajax_new.js"></script>
	<script type="text/javascript" src="js/jsFixedDeposit.js?202300923"></script>
    <script type="text/javascript" src="js/jsViewLedgerDetails.js"></script>
    <script type="text/javascript" src="js/validate.js"></script>
      <script type="text/javascript" src="js/populateData.js"></script>
	<script language="javascript" type="application/javascript">
        minStartDate = '<?php  echo getDisplayFormatDate($_SESSION['default_year_start_date']);?>';
	maxEndDate = '<?php  echo getDisplayFormatDate($_SESSION['default_year_end_date']);?>';
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
            changeMonth: true, 
    		changeYear: true,
			maxDate: maxEndDate,
			minDate: minStartDate
        })});
		
	function go_error()
    {
		document.getElementById('error').style.display = 'block';
        setTimeout('hide_error()',10000);	
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
<button type="button" class="btn btn-primary" onClick="addNew();" id="btnAdd" style="margin-bottom: 10px;">Create New FD</button>
</center>
<center>
<div id="renew_header"></div>
<!--<div id="fd_header" style="font-size:18px"></div>
--><div id="new_entry" style="display:none; width:100%;" class="fddiv">
<form name="FixedDeposit" id="FixedDeposit" method="post" action="process/FixedDeposit.process.php?module=1" onSubmit="return validateData();" class="fdform">
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
        
	   <tr>
       		<th style="width:50%;text-align:center;background-color:#337ab7; font-size:14px;color: #fff;padding-top: 5px;" colspan="4" id="fd_heading"></th>
       </tr>	
       <tr>
       		<td colspan="4" style="text-align:center"><div id="fd_header" style="font-size:18px"></div></td>
       </tr>
       <tr>
       		<td colspan="4"><br></td>
       </tr>
       <tr >
        	<td width="25%"><?php echo $star;?>FD Created In Bank Name :</td>
			<td id="FD_Bank_Name_td" width="25%">
                <select name="FD_Bank_Name" id="FD_Bank_Name" value="<?php echo $_REQUEST['FD_Bank_Name'];?>">
                	 <?php echo $fd_bank = $obj_FixedDeposit->combobox("select `id`, ledgertable.ledger_name from `ledger` as ledgertable Join `account_category` as categorytbl on  categorytbl.category_id=ledgertable.categoryid where  ledgertable.categoryid = '".BANK_ACCOUNT."' and society_id = '".$_SESSION['society_id']."' ",$_REQUEST['FD_Bank_Name'],'Please Select',0); ?>
				</select>
            </td>
            <td width="25%"><?php echo $star;?>Category :</td>
			<td id="Category_td" width="25%">
                <select name="Category" id="Category" value="<?php echo $_REQUEST['Category'];?>">
                	 <?php echo $Category = $obj_FixedDeposit->combobox("SELECT  `category_id` ,`category_name` FROM `account_category`  where `is_fd_category`= '1' ",$_REQUEST['Category'],'Please Select',0); ?>
				</select>
            </td>
		</tr>
        
<!--        <tr >
        	<td><?php //echo $star;?>Category :</td>
			<td id="Category_td">
                <select name="Category" id="Category" value="<?php //echo $_REQUEST['Category'];?>">
                	 <?php //echo $Category = $obj_FixedDeposit->combobox("SELECT  `category_id` ,`category_name` FROM `account_category`  where `is_fd_category`= '1' ",$_REQUEST['Category'],'Please Select',0); ?>
				</select>
            </td>
		</tr>
-->        
        <tr align="left">
        	<td><?php echo $star;?>FD Name :</td>
			<td id="FD_Name_td"><input type="text" name="FD_Name" id="FD_Name" value="<?php echo $_REQUEST['FD_Name'];?>" onKeyPress="return blockSpecialChars(event);"/></td> 
            <td><?php echo $star;?>FDR No :</td>
                <td id="FDR_No_td"><input type="text" name="FDR_No" id="FDR_No" value="<?php echo $_REQUEST['FDR_No'];?>"  onkeypress="return blockSpecialChars(event);"/></td> 
        </tr>
        
        <!--<tr>
        		<td><?php //echo $star;?>FDR No :</td>
                <td id="FDR_No_td"><input type="text" name="FDR_No" id="FDR_No" value="<?php //echo $_REQUEST['FDR_No'];?>"  onkeypress="return blockSpecialChars(event);"/></td> 
        </tr>-->
		
         <tr>
			<td><?php echo $star;?>Date of Deposit :</td>
			<td id="Deposit_Date_td"><input type="text" name="Deposit_Date" id="Deposit_Date" class="basics2" onChange="ValidateDate(this.id,true);"    value="<?php echo $_REQUEST['Deposit_Date'];?>"    /></td>
            <td><?php echo $star;?>Date of Maturity :</td>
			<td id="Maturity_Date_td"><input type="text" name="Maturity_Date" id="Maturity_Date" class="basics" onChange="ValidateDate(this.id,false);"  value="<?php echo $_REQUEST['Maturity_Date'];?>"    /></td>
		</tr>
        
         <!--<tr>
			<td><?php //echo $star;?>Date of Maturity :</td>
			<td id="Maturity_Date_td"><input type="text" name="Maturity_Date" id="Maturity_Date" class="basics" onChange="ValidateDate(this.id,false);"  value="<?php //echo $_REQUEST['Maturity_Date'];?>"    /></td>
		</tr>-->
        
        <tr>
			<td>FD Period:</td>
			<td id="FD_Period_td"><input type="text" name="FD_Period" id="FD_Period"  value="<?php echo $_REQUEST['FD_Period'];?>"/></td>
            <td><?php echo $star;?>Rate of Interest (%)( p.a.) :</td>
			<td id="Interest_Rate_td"><input type="text" name="Interest_Rate" id="Interest_Rate" value="<?php echo $_REQUEST['Interest_Rate'];?>"   onBlur="extractNumber(this,2,false);" onKeyUp="extractNumber(this,2,false);" onKeyPress="return blockNonNumbers(this, event, true, false);return blockSpecialChars(event);"/></td>
		</tr>
        
        <tr>
        		<td><?php echo $star;?>Principal Amount (Rs.) :</td>
                <td id="Principal_Amount_td"><input type="text" name="Principal_Amount" id="Principal_Amount" value="<?php //echo number_format($_REQUEST['Principal_Amount'],2);?>"  onBlur="extractNumber(this,2,false);" onKeyUp="extractNumber(this,2,false);" onKeyPress="return blockNonNumbers(this, event, true, false);"/></td> 
                <td><?php echo $star;?>Maturity Amount (Rs.) :</td>
                <td id="Maturity_Amount_td"><input type="text" name="Maturity_Amount" id="Maturity_Amount" value="<?php echo $_REQUEST['Maturity_Amount'];?>"  onBlur="extractNumber(this,2,false);" onKeyUp="extractNumber(this,2,false);" onKeyPress="return blockNonNumbers(this, event, true, false);"/></td>
        </tr>
            
		<!--<tr>
			<td><?php //echo $star;?>Rate of Interest (%)( p.a.) :</td>
			<td id="Interest_Rate_td"><input type="text" name="Interest_Rate" id="Interest_Rate" value="<?php //echo $_REQUEST['Interest_Rate'];?>"   onBlur="extractNumber(this,2,false);" onKeyUp="extractNumber(this,2,false);" onKeyPress="return blockNonNumbers(this, event, true, false);return blockSpecialChars(event);"/></td>
		</tr>-->
            
		<!--<tr>
        		<td><?php //echo $star;?>Maturity Amount (Rs.) :</td>
                <td id="Maturity_Amount_td"><input type="text" name="Maturity_Amount" id="Maturity_Amount" value="<?php //echo $_REQUEST['Maturity_Amount'];?>"  onBlur="extractNumber(this,2,false);" onKeyUp="extractNumber(this,2,false);" onKeyPress="return blockNonNumbers(this, event, true, false);"/></td> 
        </tr>-->
      
        <!--<tr><td colspan="4"><br></td></tr>-->
        <tr align="left">
        	<td><?php echo $star;?>Note :</td>
			<td colspan="3" style="font-size:14px;" id="Note_td"><textarea id="Note" name="Note" style="width:200px; height:60px;border: 1px solid;"  maxlength="1000" ><?php echo $_REQUEST['Note'];?></textarea></td>
        </tr>
        
      
        
    	<!--<tr  class="fd_options">
            <td colspan="2">
                <table style="width:53%;" align="center" >
                    <!--<tr>
                    	<!--<td>Renew FD A/c : </td><td><input type="checkbox" name="FD_Renew" id="FD_Renew" value="1"  disabled  style="margin-top: 1px;" onClick="validateRenew();"  <?php //echo $_REQUEST['FD_Renew'] == '1' ? checked : '';?>/>&nbsp;&nbsp;&nbsp;</td>
                         <td>Close FD A/c : </td><td><input type="checkbox" name="FD_Close" id="FD_Close" value="1" disabled   style="margin-top: 7px;"onClick="validateClose();"  <?php //echo $_REQUEST['FD_Close'] == '1' ? checked : ''; ?>/></td>
                    </tr>
                    <tr>
                    	<!--<td>Renew FD A/c : </td><td><input type="checkbox" name="FD_Renew" id="FD_Renew" value="1"  disabled  style="margin-top: 1px;" onClick="validateRenew();"  <?php //echo $_REQUEST['FD_Renew'] == '1' ? checked : '';?>/>&nbsp;&nbsp;&nbsp;</td>-->
                         <!--<td>Payout to Bank A/c : </td><td><input type="checkbox" name="FD_Bank_Payout" id="FD_Bank_Payout" value="1"    style="margin-top: 7px;" onClick="validateClose();" onChange="hide_accrued_int();"  <?php //echo $_REQUEST['FD_Close'] == '1' ? checked : ''; ?>/></td>
                    </tr>
               </table>
            </td>
        </tr>-->
		
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
            	<input type='text' id='ChequeNumber' name='ChequeNumber' onBlur='extractNumber(this,0,false);' onKeyUp='extractNumber(this,0,false);' onKeyPress='return blockNonNumbers(this, event, false, false);' style='width:80px;'  value="<?php //echo $_REQUEST['ChequeNumber'];?>"/>
             </td>
        </tr>-->
         <tr><td colspan="4" style="text-align:center" ><button type="button" class="btn btn-primary" onClick="EditFD()" id="btnEditFD" style="margin-bottom: 10px;">Edit</button>
<button type="button" class="btn btn-primary" onClick="UpdateFD(<?php echo $_REQUEST['edt']?>, <?php echo $_REQUEST['fd_id']?>)" id="btnUpdateFD" style="margin-bottom: 10px;display:none">Update</button>
<button type="button" class="btn btn-primary" onClick="Cancle();" id="btncancle" style="margin-bottom: 10px;display:none">Cancle</button>
</td></tr>
      
         <tr><td colspan="2" ><BR/></td></tr>
   </table>
   <?php 
   if($_SESSION['login_id'] == 4)
   {
   ?>
   <input type="button" name="link_vouchers" id="link_vouchers" value="Link Vouchers" onClick="window.open('ledger_voucher_detail.php?fd_id=<?php echo trim($_REQUEST['fd_id']) ?>','_blank')" />
   <?php
   }
   ?>
   <div id="put_voucher_table"></div>
   <br><br>
  <table style="width:100%; display:none;" id="int_table">
        <tr align="center" height="30px">
               <th style="width:50%;text-align:center;background-color:#337ab7; font-size:14px;color: #fff;padding-top: 5px;" colspan="4">Interest Processing</th>     	
               <!--<th style="width:50%;text-align:center;background-color:#337ab7; font-size:14px;color: #fff;padding-top: 5px;" colspan="2">Ledger Name</th>-->
               <!--<th style="width:20%;text-align:center;background-color:#337ab7;font-size:14px;color: #fff;padding-top: 5px;">Amount  (Rs.)</th>-->
         </tr>
        
        <tr id="Payout_tr">
                    	<!--<td>Renew FD A/c : </td><td><input type="checkbox" name="FD_Renew" id="FD_Renew" value="1"  disabled  style="margin-top: 1px;" onClick="validateRenew();"  <?php //echo $_REQUEST['FD_Renew'] == '1' ? checked : '';?>/>&nbsp;&nbsp;&nbsp;</td>-->
                         <td  style="text-align:center;width:50%;">Payout to Bank A/c : </td>
                         <td  style="text-align:center;width:25%;"><input type="checkbox" name="FD_Bank_Payout" id="FD_Bank_Payout" value="1"    style="margin-top: 7px;" onChange="hide_accrued_int();"  <?php //echo $_REQUEST['FD_Close'] == '1' ? checked : ''; ?>/></td>
                         <td style="width:25%;"></td>
                    </tr>
                    
        <tr id="Close_tr">
                    	<!--<td>Renew FD A/c : </td><td><input type="checkbox" name="FD_Renew" id="FD_Renew" value="1"  disabled  style="margin-top: 1px;" onClick="validateRenew();"  <?php //echo $_REQUEST['FD_Renew'] == '1' ? checked : '';?>/>&nbsp;&nbsp;&nbsp;</td>-->
                         <td style="text-align:center;width:50%;" >Close FD A/c : </td>
                         <td  style="text-align:center;width:25%;"><input type="checkbox" name="FD_Close" id="FD_Close" value="1" <?php /*?>onClick="validateClose(<?php echo $_REQUEST['edt']; ?>);"<?php */?> onChange="update_mode(<?php echo $_REQUEST['fd_id']; ?>);"  <?php //echo $_REQUEST['FD_Close'] == '1' ? checked : ''; ?>/></td>
                         <td style="text-align:left;width:25%;"></td>
         </tr>
         <tr id="Renew_tr">
                         <td width="50%" style="text-align:center;width:50%;">Renew FD A/c : </td>
                         <td style="text-align:center;width:25%;"><input type="checkbox" name="FD_Renew" id="FD_Renew" value="1" onClick="for_renew(<?php echo $_REQUEST['fd_id']; ?>);" /></td>
                         <td style="text-align:left;width:25%;"></td>
        </tr>
        
   		
        <tr id="accrued_interest_tr">
                <td style="text-align:center;width:50%;"><?php echo $star;?>Accrued Interest on FD  (Asset)</td>
                <td style="text-align:center;width:25%;">
                    <select name="accrued_interest_legder" id="accrued_interest_legder"  value="<?php //echo $_REQUEST['accrued_interest_legder'];?>" >
                         <?php echo $accrued_interest_legder = $obj_FixedDeposit->combobox("select `id`,concat_ws(' - ', ledgertable.ledger_name,categorytbl.category_name,ledgertable.id)  from `ledger` as ledgertable Join `account_category` as categorytbl on  categorytbl.category_id=ledgertable.categoryid where  categorytbl.group_id = '".ASSET."' and ledgertable.categoryid <> '".DUE_FROM_MEMBERS."'and society_id = '".$_SESSION['society_id']."' ORDER BY ledgertable.ledger_name",$_REQUEST['accrued_interest_legder'],'Please Select',0); ?>
                    </select>
                </td>
                <td style="text-align:left;width:25%;"></td>
                <!--<td><input type="text" name="accrued_interest_amt" id="accrued_interest_amt" value="<?php //echo $_REQUEST['accrued_interest_amt'];?>"  onBlur="extractNumber(this,2,false);" onKeyUp="extractNumber(this,2,false);" onKeyPress="return blockNonNumbers(this, event, true, false);"/></td> -->
        </tr>
        <tr id="on_close" >
        	<td style="text-align:center;width:50%;"><?php echo $star;?>Accured Int Amount :</td>
        	<td style="text-align:center;width:25%;">
            <?php if($_REQUEST['accrued_interest_amt'] <> '')
			{
				$accrude_intrest_amt = $_REQUEST['accrued_interest_amt'];
			} 
			else
			{
				$accrude_intrest_amt = number_format((float)0, 2, '.', '');
			}?>
            <input type="text" name="accrued_interest_amt" id="accrued_interest_amt" value="<?php echo $accrude_intrest_amt;?>"  onBlur="extractNumber(this,2,false);" onKeyUp="extractNumber(this,2,false);" onKeyPress="return blockNonNumbers(this, event, true, false);" /></td>
            <td style="text-align:left;width:25%;"></td>
        </tr>
        <tr >
                <td style="text-align:center;width:50%;"><?php echo $star;?>Interest on FD   (Liability/Income)</td>
                <td style="text-align:center;width:25%;">
                    <select name="interest_legder" id="interest_legder"  value="<?php //echo $_REQUEST['interest_legder'];?>" >
                         <?php echo $interest_legder = $obj_FixedDeposit->combobox("select `id`,concat_ws(' - ', ledgertable.ledger_name,categorytbl.category_name,categorytbl.group_id,ledgertable.id)  from `ledger` as ledgertable Join `account_category` as categorytbl on  categorytbl.category_id=ledgertable.categoryid where categorytbl.group_id = '".LIABILITY."' or categorytbl.group_id = '".INCOME."' ORDER BY ledgertable.ledger_name",$_REQUEST['interest_legder'],'Please Select',0);
						
						  ?>
                    </select>
                </td>
                <td style="text-align:left;width:25%;"></td>
                <!--<td><input type="text" name="interest_amt" id="interest_amt" value="<?php //echo $_REQUEST['interest_amt'];?>"  onBlur="extractNumber(this,2,false);" onKeyUp="extractNumber(this,2,false);" onKeyPress="return blockNonNumbers(this, event, true, false);" /></td> -->
        </tr>
        
        <tr>
        	<td style="text-align:center;width:50%;"><?php echo $star;?>Interest Amount :</td>
			 <?php if($_REQUEST['interest_amt'] <> '')
			{
				$intrest_amt = $_REQUEST['interest_amt'];
			} 
			else
			{
				$intrest_amt = number_format((float)0, 2, '.', '');
			}?>
        	<td style="text-align:center;width:25%;"><input type="text" name="interest_amt" id="interest_amt" value="<?php echo $intrest_amt;?>"  onBlur="extractNumber(this,2,false);" onKeyUp="extractNumber(this,2,false);" onKeyPress="return blockNonNumbers(this, event, true, false);" /></td>
            <td style="text-align:left;width:25%;"></td>
        </tr>
        
		<tr >
                <td style="text-align:center;width:50%;"><?php //echo $star;?>TDS Payable</td>
                <td style="text-align:center;width:25%;">
                    <select name="tds_legder" id="tds_legder"  value="<?php //echo $_REQUEST['interest_legder'];?>" >
                        <?php echo $tds_legder = $obj_FixedDeposit->combobox("select `id`,concat_ws(' - ', ledgertable.ledger_name,categorytbl.category_name) from `ledger` as ledgertable Join `account_category` as categorytbl on categorytbl.category_id=ledgertable.categoryid where ledgertable.society_id=".$_SESSION['society_id']." and categorytbl.group_id ='".ASSET."' and ledger_name like '%TDS%' ",TDS_RECEIVABLE);?>
						  
						  <?php //echo $tds_legder = $obj_FixedDeposit->combobox("select `id`,ledger_name  from `ledger` where society_id=".$_SESSION['society_id']." and ledger_name like '%TDS%' ",$_SESSION['default_tds_receivable']);
						
						  ?>
                    </select>
                </td>
                <td style="text-align:left;width:25%;"></td>
                <!--<td><input type="text" name="interest_amt" id="interest_amt" value="<?php //echo $_REQUEST['interest_amt'];?>"  onBlur="extractNumber(this,2,false);" onKeyUp="extractNumber(this,2,false);" onKeyPress="return blockNonNumbers(this, event, true, false);" /></td> -->
        </tr>
        <tr>
        	<td style="text-align:center;width:50%;">TDS Amount :</td>
             <?php if($_SESSION['default_tds_receivable'] == 0 || $_SESSION['default_tds_receivable']== '')
			{?>
        	<td style="text-align:center;width:25%;">
            <input type="text" name="tds_amt" id="tds_amt" value="<?php echo $_REQUEST['tds_amt'];?>" onBlur="extractNumber(this,2,false);" onKeyUp="extractNumber(this,2,false);" onKeyPress="return blockNonNumbers(this, event, true, false);"  disabled style="background-color:#d3d3d347"/></td>
            <td style="text-align:left;width:25%;color: red;">Please set TDS receivable from <a href="defaults.php" style="text-decoration:none">defaults page</a></td>
            <?php }
			else
			{?>
				<td style="text-align:center;width:25%;">
            <input type="text" name="tds_amt" id="tds_amt" value="<?php echo $_REQUEST['tds_amt'];?>" onBlur="extractNumber(this,2,false);" onKeyUp="extractNumber(this,2,false);" onKeyPress="return blockNonNumbers(this, event, true, false);"  <?php echo $TDSdisable?> style="background-color:<?php echo $backgroundColor;?>"/></td>
            <td style="text-align:left;width:25%;color: red;"></td>
			<?php }?>
        </tr>
         <!--<tr><td colspan="3" ><BR/></td></tr>-->
         <tr>
			<td style="text-align:center;width:50%;"><?php echo $star;?>Date of Interest Paid/Accrued:</td>
			<td style="text-align:center;width:25%;"><input type="text" name="Interest_Date" id="Interest_Date" class="basics2" onChange="ValidateDate(this.id,true);"    value="<?php echo $_REQUEST['Interest_Date'];?>" style="width:180px"    /></td>
            <td style="text-align:left;width:25%;"></td>
		</tr>
                    <!--<tr>
                    	<!--<td>Renew FD A/c : </td><td><input type="checkbox" name="FD_Renew" id="FD_Renew" value="1"  disabled  style="margin-top: 1px;" onClick="validateRenew();"  <?php //echo $_REQUEST['FD_Renew'] == '1' ? checked : '';?>/>&nbsp;&nbsp;&nbsp;</td>
                         <td>Payout to Bank A/c : </td><td><input type="checkbox" name="FD_Bank_Payout" id="FD_Bank_Payout" value="1"    style="margin-top: 7px;"onClick="validateClose();"  <?php //echo $_REQUEST['FD_Close'] == '1' ? checked : ''; ?>/></td>
                    </tr>-->
        <tr align="left">
        	<td style="text-align:center;width:50%;"><?php echo $star;?>Description :</td>
			<td style="font-size:14px; text-align:center;width:25%;"><textarea id="Interest_Note" name="Interest_Note" style="width:200px; height:60px;border: 1px solid;"  maxlength="1000" ><?php echo $_REQUEST['Interest_Note'];?></textarea></td>
             <td style="text-align:left;width:25%;"></td>
        </tr>
        
         <tr>
         	<td colspan="4">
            	<div id="for_renew" style="display:none">
                	<table width="100%">
                    	<tr>
                        	<th style="width:100%;text-align:center;background-color:#337ab7; font-size:14px;color: #fff;padding-top: 5px;" colspan="4">Renew FD</th>
                        </tr> 
                        <tr>
                        	<td colspan="2" style="text-align:right">Renew FD with same Name and No.?</td>
                            <td colspan="2" style="text-align:left"><input type="checkbox" id="renew_same_fd" name="renew_same_fd" onClick="populate_fd_name_no();" /></td>
                        </tr>  		                 
                    	<tr>
                        	<td><?php echo $star;?>FD Name:</td>
                            <td><input type="text" id="FD_Name_RN" name="FD_Name_RN" /></td>
                            <td><?php echo $star;?>FDR No.:</td>
                            <td><input type="text" id="FDR_No_RN" name="FDR_No_RN" /></td>
                        </tr>
                        <tr>
                        	<td><?php echo $star;?>Date of Deposit:</td>
                            <td><input type="text" id="DoD_RN" name="DoD_RN" class="basics" value="<?php echo getDisplayFormatDate($data[0]['maturity_date']); ?>" /></td>
                            <td><?php echo $star;?>Date of Maturity:</td>
                            <td><input type="text" id="DoM_RN" name="DoM_RN" class="basics"/></td>
                        </tr>
                        <tr>
                        	<td><?php echo $star;?>Principal Amount:</td>
                            <td><input type="text" id="principal_amt_RN" name="principal_amt_RN" value="<?php echo $data[0]['maturity_amt']; ?>"  onBlur="extractNumber(this,2,false);" onKeyUp="extractNumber(this,2,false);" onKeyPress="return blockNonNumbers(this, event, true, false);"/></td>
                          	<td><?php echo $star;?>Maturity Amount:</td>
                            <td><input type="text" id="maturity_amt_RN" name="maturity_amt_RN" onBlur="extractNumber(this,2,false);" onKeyUp="extractNumber(this,2,false);" onKeyPress="return blockNonNumbers(this, event, true, false);" /></td>
                        </tr>
                         
                        <tr>
                          	<td><?php echo $star;?>Rate of Interest:</td>
                            <td><input type="text" id="ROI_RN" name="ROI_RN" /></td>
                        	
                            <!--<td><?php //echo $star;?>Note:</td>
                            <td><textarea id="Note_RN" name="Note_RN" ></textarea></td>-->
                        </tr>
                    </table>
                </div>
            </td>
         </tr>
        
  </table>
   <table>      
        <tr>
            <td colspan="2" align="center">
                <input type="hidden" name="id" id="id"   value="<?php echo $_REQUEST['id'];?>" />
                <input type="hidden" name="mode" id="mode"   value="<?php echo $_REQUEST['mode'];?>" />
                <input type="hidden" name="ref" id="ref"   value="<?php echo $_REQUEST["fd_id"]; ?>" />
                <input type="hidden" name="freezyear" id="freezyear"   value="<?php echo $_SESSION['is_year_freeze']; ?>" />
                <input type="hidden" name="fd_readonly" id="fd_readonly"   value="<?php echo $_REQUEST["fdreadonly"]; ?>" />
                <input type="hidden" name="fd_status" id="fd_status"   value='<?php echo $_REQUEST["status"]; ?>' />
                 <input type="hidden" name="IsCallUpdtCnt" id="IsCallUpdtCnt" value="1" />
                <center>
                <?php if($_SESSION['is_year_freeze'] == 0)
				{?>
                	<input type="submit" class="btn btn-primary" name="insert" id="insert" value="Insert"  style="padding: 6px 12px; color:#fff;background-color: #2e6da4;"/>
               <input type="button" name="cancel" class="btn btn-primary"  id="cancel" value="Cancel" onClick="window.location.href='FixedDeposit.php'" style="padding: 6px 12px; color:#fff;" />
               <?php 
			   }
			   else 
			   {?>
				   <input type="submit" class="btn btn-primary" name="insert" id="insert" value="Insert"  style="padding: 6px 12px; color:#fff;background-color: #2e6da4;" disabled/>
               <input type="button" name="cancel" class="btn btn-primary"  id="cancel" value="Cancel" onClick="window.location.href='FixedDeposit.php'" style="padding: 6px 12px; color:#fff;" disabled/>
			   <?php
               }?>
            	</center>
            </td>
        </tr>
</table>

</form>
</div>
<div id="status" style="display:none;">
<div id="status_msg"></div></div>

<!--<table align="center">
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
//echo "<br>";
?>
<div id='showTable' style="font-weight:lighter;"></div>
<?php 
//echo "<br>";
?>
</td>
</tr>
</table>-->
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
if(isset($_REQUEST['edt']) && $_REQUEST['edt'] <> '')
{
?>
<script type="text/javascript">
$('document').ready(function(e) {
	
    getDetails('edit-' + <?php echo $_REQUEST['edt'] ?>+'#'+<?php echo $_REQUEST['fd_id'] ?>); 
});

$(document).ready(function(){
  	document.getElementById('Deposit_Date').disabled = true;
 	document.getElementById('Maturity_Date').disabled = true;
 	document.getElementById('FD_Period').disabled = true;
  	document.getElementById('Interest_Rate').disabled = true;
 	document.getElementById('Principal_Amount').disabled = true;
 	document.getElementById('Maturity_Amount').disabled = true;
  	document.getElementById('Deposit_Date').style.backgroundColor= "#d3d3d32e";
   	document.getElementById('Maturity_Date').style.backgroundColor= "#d3d3d32e";
   	document.getElementById('FD_Period').style.backgroundColor= "#d3d3d32e";
    document.getElementById('Interest_Rate').style.backgroundColor= "#d3d3d32e";
 	document.getElementById('Principal_Amount').style.backgroundColor= "#d3d3d32e";
  	document.getElementById('Maturity_Amount').style.backgroundColor= "#d3d3d32e";
});	

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
	//fetchFDTable(1);
</script>
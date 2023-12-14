
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
<title>W2S - Set Defaults</title>
</head>


<?php //include_once "ses_set_default.php"; ?>
<?php

?>
<?php
	include_once("includes/head_s.php");
	include_once("classes/dbconst.class.php");
	include_once("classes/utility.class.php");
	include_once("classes/defaults.class.php");
	include_once("classes/include/dbop.class.php");
	$m_dbConnRoot = new dbop(true);
	$obj_defaults = new defaults($m_dbConn,$m_dbConnRoot);
	$obj_utility = new utility($m_dbConn,$m_dbConnRoot);
	
	$displayGST=$obj_defaults->getGSTapply();
//	var_dump($_SESSION);

	$default_society = $_SESSION['society_id'];
	$star = "<font color='#FF0000'>* &nbsp;</font>";
	if(isset($_REQUEST['sid']))
	{
		if($_REQUEST['sid'] == 'new')
		{
			$obj_defaults->getDefaults(0, true);
			?>
            	<script>window.location.href = "import.php"</script>
            <?php
		}
		else
		{
			$default_society = $_REQUEST['sid'];
		}
		
	}
	
	$default_year = 0;
	$default_period = 0;
	$default_interest_on_principle = 0;
	$default_penalty_to_member = 0;
	$default_bank_charges = 0;
	$default_tds_payable = 0;
	$default_tds_receivable = 0;
	$default_impose_fine = 0;  /// impose fine
	$default_current_asset = 0;
	$default_fixed_asset = 0;
	$default_bank_account = 0;
	$default_cash_account = 0;
	$default_due_from_member = 0;
	$default_income_expenditure_account = 0;
	$default_adjustment_credit = 0;
   	$default_suspense_account = 0;
	$default_ledger_round_off = 0;
	$igst_service_tax = 0;
	$cgst_service_tax = 0;
	$sgst_service_tax = 0;
	$cess_service_tax = 0;
	$cgst_input = 0;
	$sgst_input =0;
	$igst_input = 0;
	$default_sinking_fund = 0;
	$default_investment_register = 0;
	//$defaultEmailID = '';
		
	$defaultValues = $obj_defaults->getDefaults($default_society, false);
	$defaultValues[0][APP_DEFAULT_YEAR] = $_SESSION['default_year'];
	
	if($defaultValues <> '')
	{
		$default_year = $defaultValues[0][APP_DEFAULT_YEAR];
		$default_period = $defaultValues[0][APP_DEFAULT_PERIOD];
		$default_interest_on_principle = $defaultValues[0][APP_DEFAULT_INTEREST_ON_PRINCIPLE_DUE];
		$default_penalty_to_member = $defaultValues[0][APP_DEFAULT_PENALTY_TO_MEMBER];
		$default_bank_charges = $defaultValues[0][APP_DEFAULT_BANK_CHARGES];
		$default_tds_payable = $defaultValues[0][APP_DEFAULT_TDS_PAYABLE];
		$default_tds_receivable = $defaultValues[0][APP_DEFAULT_TDS_RECEIVABLE];
		$default_impose_fine = $defaultValues[0][APP_DEFAULT_IMPOSE_FINE];    // impose fine
		$default_current_asset = $defaultValues[0][APP_DEFAULT_CURRENT_ASSET];
		$default_fixed_asset = $defaultValues[0][APP_DEFAULT_FIXED_ASSET];
		$default_bank_account = $defaultValues[0][APP_DEFAULT_BANK_ACCOUNT];
		$default_cash_account = $defaultValues[0][APP_DEFAULT_CASH_ACCOUNT];
		$default_due_from_member = $defaultValues[0][APP_DEFAULT_DUE_FROM_MEMBERS];
		$default_contribution_from_member = $defaultValues[0][APP_DEFAULT_CONTRIBUTION_FROM_MEMBERS];
		$default_Sundry_debetor = $defaultValues[0][APP_DEFAULT_SUNDRY_DEBETOR];
		$default_income_expenditure_account = $defaultValues[0][APP_DEFAULT_INCOME_EXPENDITURE_ACCOUNT];
		$default_adjustment_credit = $defaultValues[0][APP_DEFAULT_ADJUSTMENT_CREDIT];
		$default_suspense_account = $defaultValues[0][APP_DEFAULT_SUSPENSE_ACCOUNT];
		$default_ledger_round_off = $defaultValues[0][APP_DEFAULT_LEDGER_ROUND_OFF];
        $igst_service_tax = $defaultValues[0][APP_DEFAULT_IGST];
		$cgst_service_tax = $defaultValues[0][APP_DEFAULT_CGST];
		$sgst_service_tax = $defaultValues[0][APP_DEFAULT_SGST];
		$cess_service_tax = $defaultValues[0][APP_DEFAULT_CESS];
		$cgst_input = $defaultValues[0][APP_DEFAULT_INPUT_CGST];
		$sgst_input = $defaultValues[0][APP_DEFAULT_INPUT_SGST];
		$igst_input = $defaultValues[0][APP_DEFAULT_INPUT_IGST];
		$default_Sundry_creditor = $defaultValues[0][APP_DEFAULT_SUNDRY_CREDITOR];
		$default_sinking_fund = $defaultValues[0][APP_DEFAULT_SINKING_FUND];
		$default_investment_register  = $defaultValues[0][APP_DEFAULT_INVESTMENT_REGISTER];
		//$defaultEmailID = $defaultValues[0][APP_DEFAULT_EMAILID];
	}
	 if($_SESSION['profile'][PROFILE_MANAGE_MASTER] == 1 && $_SESSION['role'] == ROLE_SUPER_ADMIN)
	 {
	 		$attrDisplay = ''; 
	 }
	 else
	 {
	  		$attrDisplay = 'disabled'; 
	}
	
	$LedgerDetails = $obj_utility->GetBankLedger($default_bank_account);

?>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
    <script type="text/javascript" src="js/jquery_min.js"></script>
	<script type="text/javascript" src="js/ajax_new.js"></script>
	<script type="text/javascript" src="js/populateData.js"></script>    
	<script type="text/javascript" src="js/defaults_20190913.js"></script>
    <script type="text/javascript" src="js/jsCommon_20190326.js"></script>
    <script language="javascript" type="application/javascript">
	$(function() {
    //console.log( "ready!" );
	FreezYear('<?php echo $_SESSION['default_year']?>');
});
	function go_error()
    {
		$(document).ready(function()
		{
			$("#error").fadeIn("slow");
		});
        setTimeout('hide_error()',5000);	
    }
    function hide_error()
    {
		$(document).ready(function()
		{
			$("#error").fadeOut("slow");
		});
    }
	function checkGST(){
		
		var Apply_GST = <?php echo $displayGST[0]['apply_service_tax']; ?>;
		//alert(Apply_GST);
		
		if(Apply_GST=='1')
		{	
			
			document.getElementById('GSTDefault').style.display='block';
			document.getElementById('GSTDefault1').style.display='block';
			document.getElementById('GSTDefault2').style.display='block';
			document.getElementById('GSTDefault3').style.display='block';
			document.getElementById('GSTDefault4').style.display='block';
			document.getElementById('GSTDefault5').style.display='block';
			document.getElementById('GSTDefault6').style.display='block';
			document.getElementById('GSTDefault7').style.display='block';
			document.getElementById('igst_service_tax').style.display='block';
			document.getElementById('cgst_service_tax').style.display='block';
			document.getElementById('sgst_service_tax').style.display='block';
			document.getElementById('cess_service_tax').style.display='block';
			document.getElementById('cgst_input').style.display='block';
			document.getElementById('sgst_input').style.display='block';
			document.getElementById('igst_input').style.display='block';
		}
		else
		{
			
			document.getElementById('GSTDefault').style.display='none';
			document.getElementById('GSTDefault1').style.display='none';
			document.getElementById('GSTDefault2').style.display='none';
			document.getElementById('GSTDefault3').style.display='none';
			document.getElementById('GSTDefault4').style.display='none';
			document.getElementById('GSTDefault5').style.display='none';
			document.getElementById('GSTDefault6').style.display='none';
			document.getElementById('GSTDefault7').style.display='none';
			document.getElementById('igst_service_tax').style.display='none';
			document.getElementById('cgst_service_tax').style.display='none';
			document.getElementById('sgst_service_tax').style.display='none';
			document.getElementById('cess_service_tax').style.display='none';
			document.getElementById('cgst_input').style.display='none';
			document.getElementById('sgst_input').style.display='none';
			document.getElementById('igst_input').style.display='none';
		}
}
	</script>
<style>
   select[disabled]
   {
  		background-color:#D3D3D3;
	}
</style> 
</head>
<body onLoad="checkGST()">

<?php
	//include_once('classes/dbconst.class.php');
?>
<br>
<div class="panel panel-info" id="panel" style="display:none">
<div class="panel-heading" id="pageheader">Set Defaults</div>

<center>
	<div id="error" style="color:#CC0000;font-weight:bold;"></div>
    <br>
    <table align='center'>
		<tr><td colspan="2" style="text-align:center;font-weight:bold;font-size:14px;padding-bottom:5px;color:#0033FF;">Current Society</td></tr>
        <tr>
			<input type="hidden" name="default_society" id="default_society" value="<?php echo $_SESSION['society_id']; ?>" />
            <td colspan="2" style="font-weight:bold;"><?php echo $obj_defaults->getSocietyName($_SESSION['society_id']); ?><a href="initialize.php">&nbsp;[Change]</a></td>
		</tr>
    </table>
    
    <br>
    <table align='center'>    
        <tr><td colspan="2" style="text-align:center;font-weight:bold;font-size:14px;padding-bottom:5px;color:#0033FF;">Year Defaults</td></tr>
        <tr>
			<td>Current Year : &nbsp;</td>
			<td><select name="default_year" id="default_year" onchange="FreezYear(this.value);">
            	<?php
					if($default_year <> 0)
					{ 
						echo $combo_year = $obj_defaults->combobox("select YearID, YearDescription from year where status = 'Y' and YearID >='".$_SESSION['society_creation_yearid']."' ORDER BY YearID DESC", $default_year); 
                    }
                    else
                    {
						echo $combo_year = $obj_defaults->combobox("select YearID, YearDescription from year where status = 'Y' and YearID >='".$_SESSION['society_creation_yearid']."' ORDER BY YearID DESC", $default_year, "Please Select"); 
                    }
				?>		
            </select>
            </td>
		</tr>
        <tr><td colspan="2" style="text-align: center; color:red" id="freezYearMsg"> </td></tr>
       </table>
       <!--<table>
        <tr>
        	<td>Current Period : &nbsp;</td>
            <td> 
            	<select name="default_period" id="default_period">
                	<?php 
						if($default_year <> 0)
						{
							//echo $combo_period = $obj_defaults->combobox("select ID, Type from period where YearID = '" . $default_year . "'", $default_period); 
						}
						else
						{
							//echo '<option value="0">Please Select</option>';
						}
					?>
                </select>
            </td>
        </tr>
   </table>-->
   <br>

   <div id="error" style="color:#CC0000;font-weight:bold;"></div>

   <table style="width:80%">
  <tr><td><br><br></td></tr>
  <tr>
  <td>
  	<table style="float:left; width:47%">
  	<tr>
  		<td colspan="2" style="text-align:center;font-weight:bold;font-size:14px;padding-bottom:5px;color:#0033FF;">Ledger Defaults</td>
  	</tr>
    <tr><td><br></td></tr>
  	<tr>
    	<td><?php echo $star;?>Interest On Principle Due : &nbsp;</td>
  		<td><select name="default_interest_on_principle" id="default_interest_on_principle"  <?php echo $attrDisplay;?>>
                	<?php 
						//echo $combo_period = $obj_defaults->combobox("select id, concat_ws(' - ', ledger_name, id) from ledger where society_id = '" . $default_society .  "' ORDER BY ledger_name ASC", $default_interest_on_principle, "Please Select"); 
						echo $combo_period = $obj_defaults->combobox("select id, concat_ws(' - ' , ledgertable.ledger_name,'(',categorytbl.category_name, ')') from `ledger` as ledgertable join `account_category` as categorytbl on categorytbl.category_id=ledgertable.categoryid where categorytbl.group_id=" . INCOME . " and society_id='" . $default_society .  "' ORDER BY ledgertable.ledger_name ASC",$default_interest_on_principle, "Please Select");
					?>
        	</select>
    	</td>
  </tr>
  <tr>
       <td><?php echo $star;?>Cheque Return Penalty : &nbsp;</td>
       <td><select name="default_penalty_to member" id="default_penalty_to_member" <?php echo $attrDisplay;?>>
                	<?php 
						//echo $combo_period = $obj_defaults->combobox("select id, concat_ws(' - ', ledger_name, id) from ledger where society_id = '" . $default_society .  "' ORDER BY ledger_name ASC", $default_penalty_to_member, "Please Select"); 
						echo $combo_period = $obj_defaults->combobox("select id, concat_ws(' - ' , ledgertable.ledger_name,'(',categorytbl.category_name, ')') from `ledger` as ledgertable join `account_category` as categorytbl on categorytbl.category_id=ledgertable.categoryid where categorytbl.group_id=" . INCOME . " and society_id='" . $default_society .  "' ORDER BY ledgertable.ledger_name ASC", $default_penalty_to_member, "Please Select"); 
					?>
            </select>
       </td>
  </tr>
  <tr>
      	<td><?php echo $star;?>Bank Charges : &nbsp;</td>
        <td><select name="default_bank_charges" id="default_bank_charges"  <?php echo $attrDisplay;?>>
                	<?php 
						//echo $combo_period = $obj_defaults->combobox("select id, concat_ws(' - ', ledger_name, id) from ledger where society_id = '" . $default_society .  "' ORDER BY ledger_name ASC", $default_bank_charges, "Please Select"); 
						echo $combo_period = $obj_defaults->combobox("select id, concat_ws(' - ' , ledgertable.ledger_name,'(',categorytbl.category_name, ')') from `ledger` as ledgertable join `account_category` as categorytbl on categorytbl.category_id=ledgertable.categoryid where categorytbl.group_id=" . EXPENSE . " and society_id='" . $default_society .  "' ORDER BY ledgertable.ledger_name ASC", $default_bank_charges, "Please Select"); 
					?>
            </select>
        </td>
  </tr>
  <tr>
     	<td><?php echo $star;?>TDS Payable : &nbsp;</td>
        <td><select name="default_tds_payable" id="default_tds_payable"  <?php echo $attrDisplay;?>>
                	<?php 
						echo $combo_period = $obj_defaults->combobox("select id, concat_ws(' - ' , ledgertable.ledger_name,'(',categorytbl.category_name, ')') from `ledger` as ledgertable join `account_category` as categorytbl on categorytbl.category_id=ledgertable.categoryid where categorytbl.group_id=" . LIABILITY . " and society_id='" . $default_society .  "' ORDER BY ledgertable.ledger_name ASC", $default_tds_payable, "Please Select"); 
					?>
                </select>
        </td>
  </tr>
  <tr>
     	<td>TDS Receivable : &nbsp;</td>
        <td><select name="default_tds_receivable" id="default_tds_receivable"  <?php echo $attrDisplay;?>>
                	<?php 
						echo $combo_period = $obj_defaults->combobox("select id, concat_ws(' - ' , ledgertable.ledger_name,'(',categorytbl.category_name, ')') from `ledger` as ledgertable join `account_category` as categorytbl on categorytbl.category_id=ledgertable.categoryid where categorytbl.group_id=" . ASSET . " and society_id='" . $default_society .  "' ORDER BY ledgertable.ledger_name ASC", $default_tds_receivable, "Please Select"); 
					?>
                </select>
        </td>
  </tr>
  <tr>
       <td><?php echo $star;?>Impose Fine : &nbsp;</td>
       <td><select name="default_impose_fine" id="default_impose_fine"  <?php echo $attrDisplay;?>>
                	<?php 
						echo $combo_period = $obj_defaults->combobox("select id, concat_ws(' - ' , ledgertable.ledger_name,'(',categorytbl.category_name, ')') from `ledger` as ledgertable join `account_category` as categorytbl on categorytbl.category_id=ledgertable.categoryid where categorytbl.group_id=" . INCOME . " and society_id='" . $default_society .  "' ORDER BY ledgertable.ledger_name ASC", $default_impose_fine, "Please Select"); 
					?>
           </select>
       </td>
  </tr>
  <tr>
    	<td><?php echo $star;?>Income & Expenditure A/C: &nbsp;</td>
        <td><select name="default_income_expenditure_account" id="default_income_expenditure_account" <?php echo $attrDisplay;?>>
                	<?php 
						echo $combo_period = $obj_defaults->combobox("select id, concat_ws(' - ' , ledgertable.ledger_name,'(',categorytbl.category_name, ')') from `ledger` as ledgertable join `account_category` as categorytbl on categorytbl.category_id=ledgertable.categoryid where categorytbl.group_id=" . LIABILITY . " and society_id='" . $default_society .  "' ORDER BY ledgertable.ledger_name ASC", $default_income_expenditure_account, "Please Select"); 
					?>
            </select>
        </td>
    </tr>
    <tr>
       	<td><?php echo $star;?>Adjustment Credit: &nbsp;</td>
        <td><select name="default_adjustment_credit" id="default_adjustment_credit" <?php echo $attrDisplay;?>>
                	<?php 
						echo $combo_period = $obj_defaults->combobox("select id, concat_ws(' - ' , ledgertable.ledger_name,'(',categorytbl.category_name, ')') from `ledger` as ledgertable join `account_category` as categorytbl on categorytbl.category_id=ledgertable.categoryid where categorytbl.group_id=" . INCOME . " and society_id='" . $default_society .  "' ORDER BY ledgertable.ledger_name ASC", $default_adjustment_credit, "Please Select"); 
					?>
           </select>
       </td>
   </tr>
   <tr>
       	<td><?php echo $star;?>Suspense A/C: &nbsp;</td>
        <td><select name="default_suspense_account" id="default_suspense_account" <?php echo $attrDisplay;?>>
                	<?php 
						echo $combo_period = $obj_defaults->combobox("select id, concat_ws(' - ' , ledgertable.ledger_name,'(',categorytbl.category_name, ')') from `ledger` as ledgertable join `account_category` as categorytbl on categorytbl.category_id=ledgertable.categoryid where  categorytbl.group_id=" . LIABILITY . " and society_id='" . $default_society .  "' ORDER BY ledgertable.ledger_name ASC", $default_suspense_account, "Please Select"); 
					?>
           </select>
       </td>
   </tr>
   <tr>
       	<td><?php //echo $star;?>Ledger Round Off: &nbsp;</td>
        <td><select name="default_ledger_round_off" id="default_ledger_round_off" <?php echo $attrDisplay;?>>
                	<?php 
						echo $combo_period = $obj_defaults->combobox("select id, concat_ws(' - ' , ledgertable.ledger_name,'(',categorytbl.category_name, ')') from `ledger` as ledgertable join `account_category` as categorytbl on categorytbl.category_id=ledgertable.categoryid where  categorytbl.group_id=" . INCOME . " and society_id='" . $default_society .  "' ORDER BY ledgertable.ledger_name ASC", $default_ledger_round_off, "Please Select"); 
					?>
           </select>
       </td>
   </tr>
   <tr>
       	<td><?php //echo $star;?>Sinking Fund: &nbsp;</td>
        <td><select name="default_sinking_fund" id="default_sinking_fund" <?php echo $attrDisplay;?>>
                	<?php 
						echo $combo_period = $obj_defaults->combobox("select id, concat_ws(' - ' , ledgertable.ledger_name,'(',categorytbl.category_name, ')') from `ledger` as ledgertable join `account_category` as categorytbl on categorytbl.category_id=ledgertable.categoryid where  categorytbl.group_id=" . INCOME . " and society_id='" . $default_society .  "' ORDER BY ledgertable.ledger_name ASC", $default_sinking_fund, "Please Select"); 
					?>
           </select>
       </td>
   </tr>
  </table>
  
  <table style="width:5%; float:left;"><tr><td></td></tr></table>
  
  <table style="float:left; width:48%">
  	<tr>
  		<td colspan="2" style="text-align:center;font-weight:bold;font-size:14px;padding-bottom:5px;color:#0033FF;">Account Category Defaults</td>
    </tr>
    <tr><td><br></td></tr>
    <tr>
       	<td><?php echo $star;?>Current Asset : &nbsp;</td>
        <td><select name="default_current_asset" id="default_current_asset"  <?php echo $attrDisplay;?>>
                	<?php 
						echo $combo_period = $obj_defaults->combobox("select category_id,category_name from account_category where group_id=".ASSET." ORDER BY category_name ASC", $default_current_asset, "Please Select"); 
					?>
            </select>
        </td>
    </tr>
    <tr>
       	<td><?php echo $star;?>Fixed Asset : &nbsp;</td>
        <td><select name="default_fixed_asset" id="default_fixed_asset"  <?php echo $attrDisplay;?>>
                	<?php 
						echo $combo_period = $obj_defaults->combobox("select category_id,category_name from account_category where group_id=".ASSET." ORDER BY category_name ASC", $default_fixed_asset, "Please Select"); 
					?>
            </select>
        </td>
    </tr>
     <tr>
        <td><?php echo $star;?>Bank Account : &nbsp;</td>
        <td><select name="default_bank_account" id="default_bank_account" <?php echo $attrDisplay;?>>
                	<?php 
						echo $combo_period = $obj_defaults->combobox("select category_id,category_name from account_category where group_id=".ASSET." ORDER BY category_name ASC", $default_bank_account, "Please Select"); 
						
						//echo $combo_period = $obj_defaults->combobox("select category_id, concat_ws(' - ', category_name, category_id) from account_category  ORDER BY category_name ASC", $default_bank_account, "Please Select"); 
					?>
            </select>
       	</td>
     </tr>
     <tr>
       	<td><?php echo $star;?>Cash Account : &nbsp;</td>
        <td><select name="default_cash_account" id="default_cash_account" <?php echo $attrDisplay;?>>
                	<?php 
						echo $combo_period = $obj_defaults->combobox("select category_id, category_name from account_category where group_id=".ASSET." ORDER BY category_name ASC", $default_cash_account, "Please Select"); 
					?>
            </select>
        </td>
    </tr>
    <tr>
       <td><?php echo $star;?>Dues From Members : &nbsp;</td>
       <td><select name="default_due_from_member" id="default_due_from_member" <?php echo $attrDisplay;?>>
                	<?php 
						echo $combo_period = $obj_defaults->combobox("select category_id,category_name from account_category where group_id=".ASSET." ORDER BY category_name ASC", $default_due_from_member, "Please Select"); 
					?>
           </select>
       </td>
    </tr>
    
    <tr>
       <td><?php ?>Sundry debtor : &nbsp;</td>
       <td><select name="default_Sundry_debtor" id="default_Sundry_debtor" <?php echo $attrDisplay;?>>
                	<?php 
						echo $combo_period = $obj_defaults->combobox("select category_id, category_name from account_category where group_id=".ASSET." ORDER BY category_name ASC", $default_Sundry_debetor, "Please Select"); 
					?>
           </select>
       </td>
    </tr>
	
	<tr>
       <td ><?php ?>Sundry Creditor : &nbsp;</td>
       <td><select name="default_Sundry_creditor" id="default_Sundry_creditor" <?php echo $attrDisplay;?>>
                	<?php 
						echo $combo_period = $obj_defaults->combobox("select category_id, category_name from account_category where group_id=".LIABILITY." ORDER BY category_name ASC", $default_Sundry_creditor, "Please Select"); 
					?>
           </select>
       </td>
    </tr>


     <tr>
       <td><?php echo $star;?>Contribution From Members : &nbsp;</td>
       <td><select name="default_contribution_from_member" id="default_contribution_from_member" <?php echo $attrDisplay;?>>
                	<?php 
						echo $combo_period = $obj_defaults->combobox("select category_id,category_name from account_category where group_id=".INCOME." ORDER BY category_name ASC", $default_contribution_from_member, "Please Select"); 
					?>
           </select>
       </td>
    </tr>

	<tr>
       	<td><?php //echo $star;?>Investment Register: &nbsp;</td>
        <td><select name="default_investment_register" id="default_investment_register" <?php echo $attrDisplay;?>>
                	<?php 
						echo $combo_period = $obj_defaults->combobox("select category_id,category_name from account_category where group_id=".ASSET." ORDER BY category_name ASC", $default_investment_register, "Please Select"); 
					?>
           </select>
       </td>
   </tr>
  </table>
  </td>
 </tr>
  <tr>
   <td><br><br></td>
  </tr>
 
 <tr>
   	<td align="center"  id="GSTDefault" name="GSTDefault" style="text-align:center;font-weight:bold;font-size:14px;padding-bottom:5px;color:#0033FF;">GST Defaults</td>
   </tr>

    <tr><td><br></td></tr>
   <tr><td>
   	<table  style="float:left; width:47%">
   		<tr>
            <td  style="padding-left:5px;" id="GSTDefault1" name="GSTDefault1">Output IGST  : &nbsp;</td>
            <td style="padding-left:65px;"><select name="igst_service_tax" id="igst_service_tax" <?php echo $attrDisplay;?>>
                    <?php 
                        
						echo $combo_period = $obj_defaults->combobox("select id, concat_ws(' - ' , ledgertable.ledger_name,'(',categorytbl.category_name, ')') from `ledger` as ledgertable join `account_category` as categorytbl on categorytbl.category_id=ledgertable.categoryid where society_id='" . $default_society .  "' ORDER BY ledgertable.ledger_name ASC", $igst_service_tax, "Please Select"); 
						//echo $combo_period = $obj_defaults->combobox("select id, concat_ws(' - ', ledger_name, id) from ledger where society_id = '" . $default_society .  "' ORDER BY ledger_name ASC", $igst_service_tax, "Please Select"); 
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td  style="padding-left:5px;" id="GSTDefault2" name="GSTDefault2">Output CGST  : &nbsp;</td>
            <td style="padding-left:65px;"><select name="cgst_service_tax" id="cgst_service_tax" <?php echo $attrDisplay;?>>
                    <?php 
                       // echo $combo_period = $obj_defaults->combobox("select id, concat_ws(' - ', ledger_name, id) from ledger where society_id = '" . $default_society .  "' ORDER BY ledger_name ASC", $cgst_service_tax, "Please Select");
						echo $combo_period = $obj_defaults->combobox("select id, concat_ws(' - ' , ledgertable.ledger_name,'(',categorytbl.category_name, ')') from `ledger` as ledgertable join `account_category` as categorytbl on categorytbl.category_id=ledgertable.categoryid where society_id='" . $default_society .  "' ORDER BY ledgertable.ledger_name ASC", $cgst_service_tax, "Please Select");  
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td style="padding-left:5px;" id="GSTDefault3" name="GSTDefault3">Output SGST  : &nbsp;</td>
            <td style="padding-left:65px;"><select name="sgst_service_tax" id="sgst_service_tax" <?php echo $attrDisplay;?>>
                    <?php 
                        echo $combo_period = $obj_defaults->combobox("select id, concat_ws(' - ' , ledgertable.ledger_name,'(',categorytbl.category_name, ')') from `ledger` as ledgertable join `account_category` as categorytbl on categorytbl.category_id=ledgertable.categoryid where society_id='" . $default_society .  "' ORDER BY ledgertable.ledger_name ASC", $sgst_service_tax, "Please Select");
						 // echo $combo_period = $obj_defaults->combobox("select id, concat_ws(' - ', ledger_name, id) from ledger where society_id = '" . $default_society .  "' ORDER BY ledger_name ASC", $sgst_service_tax, "Please Select"); 
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td style="padding-left:5px;" id="GSTDefault4" name="GSTDefaul4">Output CESS  : &nbsp;</td>
            <td style="padding-left:65px;"><select name="cess_service_tax" id="cess_service_tax" <?php echo $attrDisplay;?>>
                    <?php 
                        echo $combo_period = $obj_defaults->combobox("select id, concat_ws(' - ' , ledgertable.ledger_name,'(',categorytbl.category_name, ')') from `ledger` as ledgertable join `account_category` as categorytbl on categorytbl.category_id=ledgertable.categoryid where society_id='" . $default_society .  "' ORDER BY ledgertable.ledger_name ASC", $cess_service_tax, "Please Select"); 
						
						//echo $combo_period = $obj_defaults->combobox("select id, concat_ws(' - ', ledger_name, id) from ledger where society_id = '" . $default_society .  "' ORDER BY ledger_name ASC", $cess_service_tax, "Please Select");
                    ?>
                </select>
            </td>
        </tr>
    </table>
   
   <table style="width:5%; float:left;"><tr><td></td></tr></table>
   
   <table style="float:left; width:48%">
   
   <tr>
    	<td style="padding-left:5px;line-height: 22px;"  id="GSTDefault7" name="GSTDefault7">Input IGST  : &nbsp;</td>
        <td style="padding-left:50px;"><select name="igst_input" id="igst_input" <?php echo $attrDisplay;?>>
                    <?php 
                        echo $combo_period = $obj_defaults->combobox("select id, concat_ws(' - ' , ledgertable.ledger_name,'(',categorytbl.category_name, ')') from `ledger` as ledgertable join `account_category` as categorytbl on categorytbl.category_id=ledgertable.categoryid where society_id='" . $default_society .  "' ORDER BY ledgertable.ledger_name ASC", $igst_input, "Please Select"); 
						
						
                   
  ?>
                </select>
       </td>
    </tr>
   	<tr>
    	<td style="padding-left:5px;" id="GSTDefault5" name="GSTDefault5">Input CGST  : &nbsp;</td>
        <td style="padding-left:50px;"><select name="cgst_input" id="cgst_input" <?php echo $attrDisplay;?>>
                    <?php 
                        echo $combo_period = $obj_defaults->combobox("select id, concat_ws(' - ' , ledgertable.ledger_name,'(',categorytbl.category_name, ')') from `ledger` as ledgertable join `account_category` as categorytbl on categorytbl.category_id=ledgertable.categoryid where society_id='" . $default_society .  "' ORDER BY ledgertable.ledger_name ASC", $cgst_input, "Please Select"); 
						
						// echo $combo_period = $obj_defaults->combobox("select id, concat_ws(' - ', ledger_name, id) from ledger where society_id = '" . $default_society .  "' ORDER BY ledger_name ASC", $cgst_input, "Please Select"); 
                    ?>
                </select>
        </td>
    </tr>
    <tr>
    	<td style="padding-left:5px;"  id="GSTDefault6" name="GSTDefault6">Input SGST  : &nbsp;</td>
        <td style="padding-left:50px;"><select name="sgst_input" id="sgst_input" <?php echo $attrDisplay;?>>
                    <?php 
                        echo $combo_period = $obj_defaults->combobox("select id, concat_ws(' - ' , ledgertable.ledger_name,'(',categorytbl.category_name, ')') from `ledger` as ledgertable join `account_category` as categorytbl on categorytbl.category_id=ledgertable.categoryid where society_id='" . $default_society .  "' ORDER BY ledgertable.ledger_name ASC", $sgst_input, "Please Select"); 
						
						  //echo $combo_period = $obj_defaults->combobox("select id, concat_ws(' - ', ledger_name, id) from ledger where society_id = '" . $default_society .  "' ORDER BY ledger_name ASC", $sgst_input, "Please Select"); 
                   
  ?>
                </select>
       </td>
    </tr>
    </table>

  </td></tr>
   </table>


 
    <br><br>
    <table>
        <tr>
			<td colspan="2" align="center"><input type="button" name="insert" id="insert" value="Save" onClick="ApplyValues();" style="width:120px; color:#FFF;background-color: #337ab7;" class="btn btn-primary"></td>
		</tr>
	</table>
    <br>
    <br>
</center>
</div>
<?php include_once "includes/foot.php"; ?>

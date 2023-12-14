<?php //include_once "ses_set_s.php"; ?>
<?php
	include_once("includes/head_s.php");

?>
<?php
include_once("classes/FA_Depreciation.class.php");
$obj_fa_dep = new fa_dep($m_dbConn);
$startdate = $obj_fa_dep->FetchDate($_SESSION['default_year']); 
include_once("classes/dbconst.class.php");

$details = $obj_fa_dep->getLedgerDetails($_REQUEST['id']);
//var_dump($details );
?>
 

<html>
<head>
	<style>
		#voucher_table
		{
			border:1px solid black;
			text-align:center;
		}
		#voucher_table th
		{
			border:1px solid black;
			text-align:center;
		}
		#voucher_table td
		{
			border:1px solid black;
		}
		#voucher_table
		{
			border-collapse:collapse;
		}
		#current_year
		{
			color:#FFF;
		}
	</style>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
    <script type="text/javascript" src="js/populateData.js"></script>
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/account_subcategory.js"></script>
	<link href="css/popup.css" rel="stylesheet" type="text/css" />
    <script language="javascript" type="application/javascript">
	minStartDate = '<?php echo getDisplayFormatDate($_SESSION['default_year_start_date']);?>';
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
		$(document).ready(function()
		{
			$("#error").fadeIn("slow");
		});
        setTimeout('hide_error()',8000);	
    }
    function hide_error()
    {
		$(document).ready(function()
		{
			$("#error").fadeOut("slow");
		});
    }
	
	</script>
    <script>

	function togglePopup(id)
	{
		var popup = document.getElementById(id);
    	popup.classList.toggle('show');
	}

	function addNew()
	{
		document.getElementById('new_entry').style.display = 'block';
		document.getElementById('btnAdd').style.display = 'none';
		document.getElementById("btnPrintAll").style.display = 'none';
		document.getElementById('brtag').style.display = "none";
	}
	
	function onCancel()
	{
		document.getElementById('new_entry').style.display = 'none';
		document.getElementById('btnAdd').style.display = 'block';		
	}
	
	//Not used
	function get_opening_bal()
	{
		document.getElementById('ledgers').focus();
		var led_id = document.getElementById('led_id').value;
		var dep_type = document.getElementById('dep_method').value;
		//alert("type: " + dep_type);
		if(dep_type == 1)
		{
			var purchase_amt = document.getElementById('purchase_value').value;
			document.getElementById('opening_bal').value = purchase_amt;
		}
		else if(dep_type == 2)
		{
			$.ajax({
			url : "ajax/ajaxFixedAssetDep.ajax.php",
			type : "POST",
			dataType : "json",
			data: {"method": "fetch_opening_balance", "led_id":led_id, "dep_type":dep_type},
			success: function(data)
			{
				//alert(data);
				document.getElementById('opening_bal').value = data;
				//CKEDITOR.instances['events_desc'].setData(data);
			}
			});	
		}	
	}
	
	function recalculate_depre()
	{
		var current_opening_balance = document.getElementById('opening_bal').value;
		var first_half_balance = document.getElementById('first_half_bal').value;
		var first_half_sale_balance = document.getElementById('first_half_bal_sale').value;

		var second_half_balance = document.getElementById('second_half_bal').value;
		var second_half_sale_balance = document.getElementById('second_half_bal_sale').value;
		var dep_percent = document.getElementById('dep_per').value;
		var purchase_date = document.getElementById('purchase_date').value;
		var dep_type = document.getElementById('dep_method').value;
//		alert("type: " + dep_type);
		//alert("first_half_balance: " + first_half_balance);
		 
		if(isNaN(first_half_balance))
		{
			first_half_balance = 0;
		}
		if(isNaN(first_half_sale_balance))
		{
			first_half_sale_balance = 0;
		}
		if(isNaN(second_half_balance))
		{
			second_half_balance = 0;
		}
		if(isNaN(second_half_sale_balance))
		{
			second_half_sale_balance = 0;
		}
		
		if(dep_type == 1)
		{	
			var purchase_value = document.getElementById('purchase_value').value;
			if(purchase_value == 0)
			{
				alert("Original purchase value required");
				document.getElementById('dep_method').value = 0;
				return;
			}
			var split_date = new Array();
			split_date = purchase_date.split('-');
			var new_date = split_date[2] + "-" + split_date[1] + "-" + split_date[0];
			var date = Date.parse(new_date);
			var date1 = new Date(date);
			var year = date1.getFullYear();
			//alert(year);
			
			var date_to_compare = year + "-10-01";
			var date_parse = Date.parse(date_to_compare);
			var date_parse1 = new Date(date_parse);
			//alert(date_parse1);
			
			if(date1 > date_parse1)
			{
				//alert("After oct");
				var dep_percent = dep_percent/2;
				//alert(dep_percent);
			}
			else if(date1 < date_parse1)
			{
				//alert("Before oct");
				var dep_percent = dep_percent;
				//alert(dep_percent);
			}
			var purchase_amt = document.getElementById('purchase_value').value;
			//document.getElementById('opening_bal').value = purchase_amt;
			purchase_amt = purchase_amt ;
//			alert(purchase_amt);

			var dep_amt = ((dep_percent/100)*purchase_amt);
			var new_opening_balance = (parseFloat(current_opening_balance) + parseFloat(first_half_balance) + parseFloat(second_half_balance) - dep_amt);
			new_opening_balance=new_opening_balance;
//			alert(new_opening_balance);
		}
		else if(dep_type == 2)
		{
			var dep_amt = ((parseFloat(dep_percent)/100)* (parseFloat(current_opening_balance) + parseFloat(first_half_balance) + parseFloat(first_half_sale_balance) + (parseFloat(second_half_balance)/2) + (parseFloat(second_half_sale_balance)/2)));
		
		//alert("current_opening_balance: " + current_opening_balance);
		//alert("dep_amt: " + dep_amt);
			dep_amt = Math.round(dep_amt);

//alert(dep_amt);
			var new_opening_balance = ((parseFloat(current_opening_balance)) - dep_amt);
			var new_opening_balance = (parseFloat(current_opening_balance) + parseFloat(first_half_balance) - parseFloat(first_half_sale_balance) + parseFloat(second_half_balance) - parseFloat(second_half_sale_balance) - dep_amt);
		}	
		
		if(isNaN(new_opening_balance))
		{
			document.getElementById('new_bal').value = 0;
			document.getElementById('dep_amt').value = 0;
		}
		else
		{
			document.getElementById('new_bal').value = new_opening_balance.toFixed(2);
			document.getElementById('dep_amt').value = dep_amt.toFixed(2);
		}
	}
	
	function detele_Asset_Voucher(VoucherNo, RefNo)
	{
		
		var IsAssetDelete = confirm("Are you sure you want to delete ? ");
		
		if(IsAssetDelete == true)
		{
			$.ajax({
			url:"ajax/ajaxFixedAssetDep.ajax.php",
			type:"POST",
			data:{"method":"deleteAssetVoucher","VoucherNo":VoucherNo,"RefNo":RefNo},
			success:function(data)
			{
				var result = data.split("@@@");
				
				if(result[1] == "success")
				{
					alert("Entry Deleted successfully.");
					window.open('FixedAssetMgmt.php','_self');	
				}
				else if(result[1] == "failed")
				{
					alert("Unable to delete entry");
					window.open('FixedAssetMgmt.php','_self');
				}
				else
				{
					alert("Unable to delete entry due to "+result[1]);
					window.open('FixedAssetMgmt.php','_self');
				}
			}
			});
		}
		else
		{
			return false;
		}
	}
	
	function create_jv()
	{
		var to_ledger_id = document.getElementById('led_id').value;
		var by_ledger_id = document.getElementById('ledgers').value;
		if(by_ledger_id == 0)
		{
			alert("Depreciation ledger required");
			document.getElementById('ledgers').value = 0;
			return;
		}
		var dep_amt = document.getElementById('dep_amt').value;
		var opening_bal = document.getElementById('opening_bal').value;
		var first_half_bal = document.getElementById('first_half_bal').value;
		var second_half_bal = document.getElementById('second_half_bal').value;
		var closing_bal = document.getElementById('new_bal').value;
		var dep_per = document.getElementById('dep_per').value;
		if(dep_per == 0)
		{
			alert("Depreciation percentage required");
			document.getElementById('dep_per').value = 0;
			return;
		}
		var purchase_date = document.getElementById('purchase_date').value;
		var purchase_amt = document.getElementById('purchase_value').value;
		var depreciation_type = document.getElementById('dep_method').value;
		$.ajax({
		url : "ajax/ajaxFixedAssetDep.ajax.php",
		type : "POST",
		dataType : "json",
		data: {"method":"create_jv", "to_ledger_id":to_ledger_id, "by_ledger_id":by_ledger_id, "dep_amt":dep_amt, "opening_bal":opening_bal, "closing_bal":closing_bal, "dep_per":dep_per, "purchase_date":purchase_date, "purchase_amt":purchase_amt, "depreciation_type":depreciation_type},
		success: function(data)
		{
			alert(data);
			var result = data.split('@@@');
			//alert(result);
			
			if(result[1] == "Success")
			{
				alert("Depreciation applied successfully.");
				window.open('FixedAssetMgmt.php','_self');
			}
			else if(result[1] == "Exists")
			{
				alert("Depreciation for current year already applied.");
				window.open('FixedAssetMgmt.php','_self');
			}
			else
			{
				alert("Error applying Depreciation for current year.");
				window.open('FixedAssetMgmt.php','_self');
			}
		}
		});
	}
	
	$( document ).ready(function() {
    	var led_id = document.getElementById('led_id').value;
		
		$.ajax({
		url : "ajax/ajaxFixedAssetDep.ajax.php",
		type : "POST",
		dataType : "json",
		data: {"method":"show_jv_table", "led_id":led_id},
		success: function(data)
		{
			//alert(data);
			//if(data == '')
//			{
//				document.getElementById('show_error_already_exists').style.display = "none";
//			}
//			else
//			{
//				document.getElementById('show_error_already_exists').style.display = "table-row";
				document.getElementById('put_voucher_table').innerHTML = data;
//			}
		}
		});
	});
	
	var isSadmin = false; 
	<?php
		if($_SESSION['role'] && ($_SESSION['role']==ROLE_SUPER_ADMIN))
		{ ?>
		isSadmin = true;
	<?php } ?>
</script>
</head>

<?php if(isset($_POST['ShowData']) || isset($_REQUEST['msg'])){ ?>
<body onLoad="go_error();">
<?php } ?>

<body>
<br>
<center>
<div class="panel panel-info" id="panel" style="display:none">
        <div class="panel-heading" id="pageheader">Fixed Asset Depreciation</div>
        <br>

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
<?php
/*echo "<pre>";
print_r($_SESSION);
echo "</pre>";*/
?>
<center>
<!--<button type="button" class="btn btn-primary" onclick="window.location.href='ledger_details.php?imp'">Ledger Details</button>-->
<!--<button type="button" class="btn btn-primary" onClick="addNew();" id="btnAdd" >Add New Ledger</button>
<button type="button" class="btn btn-primary" onClick="window.open('multiple_ledger_print.php');"  id="btnPrintAll">Print Multiple Ledger</button>
<button type="button" class="btn btn-primary" onClick="window.open('account_category.php', '_blank')" id="">Manage Categories</button>
-->
<div id="brtag"><br/></div>
</center>
<form name="fixedassets_depreciation" id="fixedassets_depreciation" method="post" action="process/fixedassets_depreciation.process.php" >
	 <table width="70%">
     	<tr id="show_error_already_exists" style="display:none">
        	<td colspan="2" style="text-align:center"><font style="color:#F00">Depreciation already applied for current year.</font></td>
        </tr>
     	<tr>
        	<td colspan="2"><input type="hidden" id="led_id" name="led_id" value="<?php echo $_REQUEST['id']; ?>" /></td>
        </tr>
     	<tr>
        	<td width="50%" style="text-align:right">Ledger Name : </td>
            <td width="50%"><input type="text" id="ledger_name" name="ledger_name" value="<?php echo $details[0]['ledger_name']; ?>" /></td>
        </tr>        
        <tr>
        	<td style="text-align:right">Purchase Date : </td>
            <td><input type="text" id="purchase_date" name="purchase_date" class="basics" value="<?php echo getDisplayFormatDate($details[0]['PurchaseDate']); ?>"/></td>
        </tr>
        <tr>
        	<td style="text-align:right">Original Purchase Value (for records): </td>
            <td><input type="text" id="purchase_value" name="purchase_value" value="<?php echo $details[0]['PurchaseValue']; ?>" /></td>
        </tr>
        <tr>
        	<td style="text-align:right">Depreciation Type : </td>
            <td><select id="dep_method" name="dep_method" onChange="recalculate_depre();">
            		<option value="0" >Please Select</option>
                    <option value="1" <?php if($details[0]['DepreciationMethod'] == 1) { ?> selected <?php } ?> >Straight - Line Method</option>
                    <option value="2" <?php if($details[0]['DepreciationMethod'] == 2) { ?> selected <?php } ?> >Reducing - Balance Method</option>
            	</select>
                
                <button type="button" style="border-radius:50px; width:15px; color:#009; vertical-align:middle;" class="popup" onMouseOver="togglePopup('demo2_tip');" onMouseOut="togglePopup('demo2_tip');"><i class="fa fa-info-circle" ></i>
                	<div id="demo2_tip" class="popuptext" style="text-align:left; width:21vw;">
                    	<dl style="margin-left:10px;">
                        	<dt>Depreciation Methods, generally used,</dt>
	                        <dt><br/></dt> 
    	                    <dt>Straight - Line Method </dt>
        	                <dd>: spreads the cost of the fixed asset evenly over its useful life. Depreciation applied on the Purchase Price.</dd>
            	            <dt><br/></dt> 
                	        <dt>Reducing - Balance Method </dt>
                    	    <dd>: Depreciation applied on the opening balance of current year.</dd>                            
	                    </dl>
    	            </div>
        	    </button>
                
            </td>
        </tr>
        <?php
			$sql1 = "SELECT l.`id`, l.`ledger_name` FROM `ledger` l, `account_category` ac WHERE l.`categoryid` = ac.`category_id` AND l.`society_id` = '".$_SESSION['society_id']."' AND (ac.`group_id` = '1' || ac.`group_id` = '4')";
		?>
        <tr>
        	<td style="text-align:right">Choose Depreciation Ledger : </td>
            <td><select id="ledgers" name="ledgers" >
            		<?php echo $combobox = $obj_fa_dep->combobox($sql1,$details[0]['By'],"Please Select","0"); ?>
            	</select>
            </td>
        </tr>
        <tr>
        	<td style="text-align:right">Opening Balance : </td>
            <td><input type="text" id="opening_bal" name="opening_bal" onBlur="recalculate_depre();" value="<?php echo $details[0]['openingbalance']; ?>" /></td>
        </tr>
        <tr>
        	<td style="text-align:right">Added in first half year : </td>
            <td><input type="text" id="first_half_bal" name="first_half_bal" onBlur="recalculate_depre();" value="<?php echo $details[0]['half_year_bal']; ?>" /></td>
        </td>
        </td>
        	<td style="text-align:right">Sold : </td>
            <td><input type="text" id="first_half_bal_sale" name="first_half_bal_sale" onBlur="recalculate_depre();" value="<?php echo $details[0]['half_year_bal_sale']; ?>" /></td>
        </tr>
        <tr>
        	<td style="text-align:right">Added in second half year : </td>
            <td><input type="text" id="second_half_bal" name="second_half_bal" onBlur="recalculate_depre();" value="<?php echo $details[0]['second_half_year_bal']; ?>" /></td>
        </td>
        </td>
        	<td style="text-align:right">Sold : </td>
            <td><input type="text" id="second_half_bal_sale" name="second_half_bal_sale" onBlur="recalculate_depre();" value="<?php echo $details[0]['second_half_year_bal_sale']; ?>" /></td>
        </tr>
        <tr>
        	<td style="text-align:right">Depreciation Percentage : </td>
            <td><input type="text" id="dep_per" name="dep_per" onBlur="recalculate_depre();" value="<?php echo $details[0]['DepreciationPercent']; ?>" />
            	<button type="button" style="border-radius:50px; width:15px; color:#009; vertical-align:middle;" class="popup" onMouseOver="togglePopup('demo1_tip');" onMouseOut="togglePopup('demo1_tip');"><i class="fa fa-info-circle" ></i>
                	<div id="demo1_tip" class="popuptext" style="text-align:left; width:21vw;">
                    	<dl style="margin-left:10px;">
                        	<dt>Suggested Depreciation Percentages</dt>
	                        <dt><br/></dt> 
    	                    <dt>10% </dt>
        	                <dd>: Any furniture / fittings including electrical fittings.</dd>
            	            <dt><br/></dt> 
                	        <dt>15% </dt>
                    	    <dd>: Music system, CCTV, Water Purifier (equipments).</dd>
                            <dt><br/></dt> 
                	        <dt>60% </dt>
                    	    <dd>: Computer, printers, scanner, UPS.</dd>
	                    </dl>
    	            </div>
        	    </button>
            </td>
        </tr>
        <tr>
        	<td style="text-align:right">Depreciated Amount : </td>
            <td><input type="text" id="dep_amt" name="dep_amt" value="<?php echo $details[0]['dep_amt']; ?>" /></td>
        </tr>
        <tr>
        	<td style="text-align:right">Closing Balance : </td>
            <td><input type="text" id="new_bal" name="new_bal" value="<?php echo $details[0]['closing_bal']; ?>" /></td>
        </tr>
        <tr>
        	<td><br></td>
        </tr>
        
        <tr>
        <td colspan="4" style="text-align:center">
        <?php 
			if($_SESSION['is_year_freeze'] == 0)
			{?>
			 		<input type="button" id="update" name="update" value="Apply Depreciation" class="btn btn-primary" onClick="create_jv();" />
       				<input type="button" id="cancel" name="cancel" value="Cancel" class="btn btn-primary" onClick="window.open('FixedAssetMgmt.php','_self');">
			<?php 
			}
			else
			{?>
				 <input type="button" id="update" name="update" value="Apply Depreciation" class="btn btn-primary" disabled />
        		<input type="button" id="cancel" name="cancel" value="Cancel" class="btn btn-primary" disabled>
			<?php 
			}?>
        
        </td>
        	<!--<td style="text-align:right"><input type="button" id="update" name="update" value="Apply Depreciation" class="btn btn-primary" onClick="create_jv();" /></td>
            <td style="text-align:left"><input type="button" id="cancel" name="cancel" value="Cancel" class="btn btn-primary" onClick="window.open('FixedAssetMgmt.php','_self');"</td>-->
        </tr>
     </table>
     
     <br><br>
     <div id="put_voucher_table" ></div>

</form>
</center>
</div>

<?php include_once "includes/foot.php"; ?>
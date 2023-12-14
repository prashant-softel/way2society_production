

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
<title>W2S - Vendor</title>
</head>

<?php //include_once "ses_set_s.php"; ?>
<?php
	include_once("includes/head_s.php");

?>
<?php
include_once("classes/account_subcategory_vendor.class.php");
$obj_account_subcategory = new account_subcategory($m_dbConn);
$startdate = $obj_account_subcategory->FetchDate($_SESSION['default_year']); 
include_once("classes/dbconst.class.php");
$getcategory = $obj_account_subcategory->getcategoryId($_SESSION['default_Sundry_creditor']);
//print_r($getcategory);
$validate =  $obj_account_subcategory->validate();
?>


<?php

$opening_balance =0;
if($_REQUEST['opening_balance'] != '')
{
	$opening_balance = $_REQUEST['opening_balance'];
}
else
{
	$opening_balance =0;
}
?>
 
<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
    <script type="text/javascript" src="js/populateData.js"></script>
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/account_subcategory_vendor.js"></script>
     <script language="JavaScript" type="text/javascript" src="js/validate.js"></script> 
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
	function addNew()
	{
		document.getElementById('new_entry').style.display = 'block';
		document.getElementById('btnAdd').style.display = 'none';
		//document.getElementById("btnPrintAll").style.display = 'none';
		document.getElementById('brtag').style.display = "none";
	}
	
	function onCancel()
	{
		document.getElementById('new_entry').style.display = 'none';
		document.getElementById('btnAdd').style.display = 'block';		
	}
	
	var isSadmin = false; 
	<?php
		if($_SESSION['role'] && ($_SESSION['role']==ROLE_SUPER_ADMIN))
		{ ?>
		isSadmin = true;
	<?php } ?>
</script>
<style>
tr,td{
	padding-bottom:5px;
}
</style>
</head>

<?php if(isset($_POST['ShowData']) || isset($_REQUEST['msg'])){ ?>
<body onLoad="go_error();">
<?php } ?>

<body>
<br>
<center>
<div id="Vendor_page">

	<div class="panel panel-info" id="panel" style="display:none">
        <div class="panel-heading" id="pageheader">Vendor</div>
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
<div>
<div id="hide1">
	<center>
	<!--<button type="button" class="btn btn-primary" onclick="window.location.href='ledger_details.php?imp'">Ledger Details</button>-->
	<?php if($_SESSION['profile'][PROFILE_PAYMENTS] == 1 ||($_SESSION['role'] && ($_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['role']==ROLE_ADMIN )) && $_SESSION['is_year_freeze'] == 0){ ?> <!--If user has manage master then only user can create ledger-->
	<button type="button" class="btn btn-primary" onClick="addNew();" id="btnAdd" >Add New Vendor</button>

	<?php }?>





<div id="brtag"><br/></div>
</center>
<div id="new_entry" style="display:none;">
<form name="account_subcategory_vendor" id="account_subcategory_vendor" method="post" action="process/account_subcategory_vendor.process.php" onSubmit="return val();">
 <input type="hidden" name="edit" id="edit" value="<?php echo $_REQUEST['edt']; ?>" />

<table align='center' >
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

	
<table style="width:70%">
	
  	<tr>
 	 <td>
<!-- First table of left side-->
			<table style="width:47%; float:left;">
				<tr><td><input type="hidden" name= "society_id" id="society_id" value="<?php echo DEFAULT_SOCIETY; ?>"/></td></tr>


					<tr>
						<td>Group<?php echo $star; ?></td>
						<td><select name="groupid" id="groupid"  disabled style="background-color: #9999997d;font-weight: bold;color: black;" onChange="get_category(this.value);">		
							<?php echo $combo_society = $obj_account_subcategory->combobox("select `id`, `groupname` from `group`", $_REQUEST['groupid']); ?>
						</select>
						</td>
					</tr>
				<tr>
					<td>Category ID<?php echo $star; ?></td>
					<td><input type="hidden"  name="categoryid" id="categoryid" value="<?php echo $getcategory[0]['category_id'];?>" disabled>
					<input type="text"  name="categoryname" id="categoryname" value="<?php echo $getcategory[0]['category_name'];?>" disabled>				<!--onChange="get_subcategory()"-->
				</select>
					</td>
				</tr>

				<tr>
					<td>Ledger<?php echo $star; ?></td>
					<td><input type="text" name="ledger_name" id="ledger_name"  value="<?php echo $_REQUEST['ledger_name']?>"/></td>
				</tr>	
		<script>
			$('#ledger_name').bind('keypress', function(e) {

						if($('#ledger_name').val().length >= 0)
						{
							var k = e.keyCode;
							var ok = k >= 65 && k <= 90 || // A-Z
								k >= 97 && k <= 122 || // a-z
								k >= 48 && k <= 57 || // 0-9
								k == 32; // {space}

							if (!ok){
								e.preventDefault();
							}
						}
					}); 
				
					</script>
				<tr id="opening">
					<td >Opening Type <?php echo $star; ?></td>
					<td><select name="opening_type" id="opening_type"  style="background-color: #9999997d;font-weight: bold; color: black;" value="<?php echo $opening_balance;?>" >
						<option value="0"> Please Select </option>
						<option value="1" selected="selected"> Credit </option>
						<option value="2"> Debit </option>
					</select>
					</td>
				</tr>
				<tr id="opening_Balance">
					<td>Opening Balance<?php //echo $star; ?></td>
					<td><input type="text" name="opening_balance" id="opening_balance"  
					value="<?php echo $opening_balance?>"/>
					<input type="hidden" name="applygst" id = "applygst" value="<?php echo $_SESSION['apply_gst'] ?>">
					</td>
					
				</tr>
				<tr>
					<td>Contact No :</td>
					<td><input type="text" name="contact_no" id="contact_no"  value="<?php echo $_REQUEST['vendor_contact']?>"/></td>
				</tr>
                <tr>
					<td>Office No:</td>
					<td><input type="text" name="off_contact" id="off_contact"  value="<?php echo $_REQUEST['vendor_office_no	']?>"/></td>
				</tr>
                <tr>
					<td>Email:</td>
					<td><input type="text" name="email_add" id="email_add"  value="<?php echo $_REQUEST['vendor_email']?>"/></td>
				</tr>
				
				
				<tr>
					<td>Website Name :</td>
					<td><input type="text" name="website" id="website"  value="<?php echo $_REQUEST['vendor_pincode']?>"/></td>
				</tr>
				<tr>
					<td>Note :</td>
					<td><input type="text" name="note" id="note"  value="<?php echo $_REQUEST['note']?>"/></td>
				</tr>
			
			</table>
	<!-- Second  table of right side-->
			<table style="width:48%;float:left;" >
				<tr>
					<td><!--Date--><?php //echo $star; ?></td>
					<td><input type="hidden" name="balance_date" id="balance_date"  value="<?php if($_REQUEST['balance_date'] <> ""){echo $_REQUEST['balance_date'];}else{echo $startdate; }?>"   readonly /></td>
				</tr>
				<tr id="GSTIN_Details">
					<td>GSTIN No.<?php //echo $star; ?></td>
					<td><input type="text" name="GSTIN_No" id="GSTIN_No" style="text-transform:uppercase"  value="<?php echo $_REQUEST['GSTIN_No']?>"/></td>
				</tr>

				
				<tr id="pan_Details">
					<td>PAN No<?php //echo $star; ?></td>
					<td><input type="text" name="Pan_no" id="Pan_no" style="text-transform:uppercase"  value="<?php echo $_REQUEST['PAN_No']?>"/></td>
				</tr>
				
				<tr id="nature_Details">
					<td >Nature of Payment</td>
					<td><select name="natureOfPayment" id="natureOfPayment" value="<?php echo $_REQUEST['nature_of_payId']?>">
						<?php
							echo '<option value="">Please Select</option>'; 
							for($i = 0; $i < sizeof($NatureOfTDS);  $i++)
							{
								echo '<option value="' . $NatureOfTDS[$i]["id"] . '">' . $NatureOfTDS[$i]["id"] . ' - ' .  $NatureOfTDS[$i]["description"] . '</option>';
							}
						?>	
						<?php //echo $natureofpayment = $obj_account_subcategory->combobox1("select `tds_id`,concat_ws(' - ', `nature_name`, `desc`) from `nature_of_tds`",0); ?>
					</select></td>
				</tr>
				<tr id="nature_detail_rate">
					<td>TDS Rate (%) </td>
					<td><input type="text" name="nature_rate" id="nature_rate"  value="<?php echo $_REQUEST['nature_deduction_rate']?>" onKeyUp='extractNumber(this,2,true);'/></td>
				</tr>
				
				
                <tr>
					<td>Address 1 :</td>
					<td><textarea id="Address1" name="Address1" rows="3" cols="35" value="<?php echo $_REQUEST['vendor_address1']?>"></textarea>
					</td>
				</tr>
<?php echo $_REQUEST['vendor_address2']?>
				<tr>
					<td>Address 2 :</td>
					<td><textarea id="Address2" name="Address2" rows="3" cols="35" value="<?php echo $_REQUEST['vendor_address2']?>"></textarea></td>
				</tr>
				<tr>
					<td>Pincode :</td>
					<td><input type="text" name="Pincode" id="Pincode"  value="<?php echo $_REQUEST['website']?>"/></td>
				</tr>

				<tr>
					<td>State : </td>
					<td><input type="text" name="State" id="State"  value="<?php echo $_REQUEST['vendor_state']?>"/></td>
				</tr>

				<tr>
					<td>City :</td>
					<td><input type="text" name="City" id="City"  value="<?php echo $_REQUEST['vendor_city']?>"/></td>
				</tr>
				

			</table>
		</td></tr>
		</table>
 		<tr><td colspan="2"> <br /></td></tr>
       <table style="width:70%;margin-left: 12%;" >
            	<tr>
                	<td><input type="checkbox" name="show_in_bill" id="show_in_bill" value="1"/></td><td  style="width:20%;">Show In Maintence Bill</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td><input type="checkbox" name="income" id="income" value="1" /></td><td>Income</td>
                    <td><input type="checkbox" name="sale" id="sale" value="1" /></td><td>Sale</td>
                </tr>
                <tr>                   
                	<td><input type="checkbox" name="supplementary_bill" id="supplementary_bill" value="1"  /></td><td>Supplementary Bill</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td><input type="checkbox" name="expense" id="expense" value="1" /></td><td>Expense</td>
                    <td><input type="checkbox" name="purchase" id="purchase" value="1" /></td><td>Purchase</td>
                </tr>
                <tr>
                	<td><input type="checkbox" name="taxable" id="taxable" value="1" /></td><td>Taxable</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td><input type="checkbox" name="payment" id="payment" value="1" /></td><td>Payment</td>
                    <td><input type="checkbox" name="sec_dep" id="sec_dep" value="1" /></td><td>Security Deposit</td>
                </tr>
                <tr>
					<td><input type="checkbox" name="nothreshold" id="nothreshold" value="1" /></td><td>Taxable Without GST Threshold</td>					
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td><input type="checkbox" name="receipt" id="receipt" value="1" /></td><td>Receipt</td>
            	</tr>	            
            </table>
        </td>
        </tr>
        <tr><td colspan="2"> <br /></td></tr>
        <tr>
			<td colspan="2" align="center"><input type="hidden" name="id" id="id"><input type="submit" class="btn btn-primary" name="insert" id="insert" value="<?php if(isset($_REQUEST['insert'] ) && $_REQUEST['insert'] <> ""){echo $_REQUEST['insert'];}else {echo "Insert";} ?>" style="padding: 6px 12px; color:#fff;background-color: #2e6da4;" >
            &nbsp; <input type="button" name="cancel" class="btn btn-primary" id="cancel" value="Cancel" onClick="window.location.href='vendor.php'" style="padding: 6px 12px; color:#fff;background-color: #2e6da4;" /></td>
		</tr> 

		<script>
		var group_id=document.getElementById('groupid').value;
		
		get_category(group_id,'<?php echo $_REQUEST['categoryid'];?>');
		
         
        </script>
</table>
</form>
</div>
</div>
	<table align="center" id="hidebody">
	<tr>

		<div id="BreakPart"><?php echo str_repeat('<br>',10);?></div>
		<?php	
		if($validate <> "")
		{
			echo $validate;	
					
		}
		?>
 
	</tr>
		<tr>
		<td>
		<?php
		echo "<br>";
		$str1 = $obj_account_subcategory->pgnation();
		echo "<br>";
		$str = $obj_account_subcategory->display1($str1);
		echo "<br>";
		echo "<br>";
		?>
		</td>
		</tr>
	</table>

</div>
</div>
</center>
</body>
<script>
if(document.getElementById("applygst").value == 0)
{
	document.getElementById("GSTIN_No").disabled = true;
	document.getElementById('GSTIN_No').style.backgroundColor = 'lightgray';
}
/*function get_subcategory()
{
	var category_id = document.getElementById("categoryid").value;
	var default_bank_account = <?php echo $_SESSION['default_bank_account'] ?>;
	var default_cash_account = <?php echo $_SESSION['default_cash_account'] ?>;
	if(category_id == default_bank_account || category_id == default_cash_account)
	{
		document.getElementById('payment').checked=true;
	}
	else
	{
		//document.getElementById('payment').checked=false;
	}
	
}*/
</script>
<script>
	var category_id = document.getElementById('categoryid').value;
	//alert(category_id);

	document.getElementById('categoryname').style.backgroundColor = 'lightgray';
	document.getElementById('categoryname').disabled = true;

	//var id = $_SESSION['default_Sundry_creditor'];
	</script>

<?php 

if($_SESSION['default_Sundry_creditor'] == 0)
{

	$validate =  $obj_account_subcategory->validate(); ?>
	<script>
	
		//document.getElementById('pageheader').style.display = 'none';
		document.getElementById("hidebody").style.display = 'none';
		document.getElementById("hide1").style.display = 'none';

	</script>
<?php	//alert("Please select *Sundry Creditor = sundry creditor* from defaults page.");
//window.location.href = "defaults.php";
}
else
{?>
	<script>
	document.getElementById("BreakPart").style.display = 'none'; </script>
<?php	$validate ="";
}
?>

<?php
if(isset($_REQUEST['edt']))
{
	 
?>
<script>	
	getaccount_subcategory('edit-' + <?php echo  $_REQUEST['edt'] ?>); 
</script>

<?php    
}

if(isset($_REQUEST['opening_type']))
{
?>
<script>
	document.getElementById('opening_type').value = '<?php echo $_REQUEST['opening_type']?>';
</script>

<?php    
}

if(isset($_REQUEST['type'])  && $_REQUEST['type'] == 1)
{
?>
<script>
	document.getElementById("btnAdd").click(); 
</script>

<?php    
}
?>

<script>
	
$(document).ready(function() {
	
	printMessage = '<?php echo $sHeader?> ';
	
	 $('#example').dataTable(
	 {
		"bDestroy": true
	}).fnDestroy();
	
	var EditAccess = "<?php echo $_SESSION['profile'][PROFILE_VENDOR_MANAGEMENT] ?>";
	var nCol = [11,12,13,14,15,16,18];
	if(EditAccess == 1)
	{
		nCol = [9,10,11,12,13,14,15,16,19];
	}
	
	if(localStorage.getItem("client_id") != "" && localStorage.getItem("client_id") != 1)
	{
			$('#example').dataTable( {
							dom: 'T<"clear">Blfrtip',
							"aLengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
							buttons: 
							[
								{
									extend: 'colvis',
									width:'inherit'/*,
									collectionLayout: 'fixed three-column'*/
								}
							],
						 columnDefs: 
						 [
							{
								targets: nCol,
								visible: false
							}
						],
							"oTableTools": 
							{
								"aButtons": 
								[
									{ "sExtends": "copy", "mColumns": "visible" },
									{ "sExtends": "csv", "mColumns": "visible" },
									{ "sExtends": "xls", "mColumns": "visible" },
									{ "sExtends": "pdf", "mColumns": "visible"},
									{ "sExtends": "print", "mColumns": "visible","sMessage": printMessage + " "}
								],
							 "sRowSelect": "multi"
						},
						aaSorting : [],
							
						fnInitComplete: function ( oSettings, json ) {
							//var otb = $(".DTTT_container")
							//alert("fnInitComplete");
							$(".DTTT_container").append($(".dt-button"));
							
							//get sum of amount in column at footer by class name sum
							this.api().columns('.sum').every(function(){
							var column = this;
							var total = 0;
							var sum = column
								.data()
								.reduce(function (a, b) {
									if(a.length == 0)
									{
										a = '0.00';
									} 
									if(b.length == 0)
									{
										b = '0.00';
									}
									var val1 = parseFloat( String(a).replace(/,/g,'') ).toFixed(2);
									var val2 = parseFloat(String(b).replace(/,/g,'') ).toFixed(2);
									total = parseFloat(parseFloat(val1)+parseFloat(val2));
									return  total;
								});
						$(column.footer()).html(format(sum,2));
						});
						
						}
						
					} );	
		}
		else
		{
				$('#example').dataTable( {
							/*dom: 'T<"clear">lfrtip',*/
							dom: 'T<"clear">Blfrtip',
							"aLengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
							buttons: 
							[
								{
									extend: 'colvis',
									width:'inherit'/*,
									collectionLayout: 'fixed three-column'*/
								}
							],
						 columnDefs: 
						 [
							{
								targets: nCol,
								visible: false
							}
						],
							"oTableTools": 
							{
								"aButtons": 
								[
									{ "sExtends": "copy", "mColumns": "visible" },
									{ "sExtends": "csv", "mColumns": "visible" },
									{ "sExtends": "xls", "mColumns": "visible" },
									{ "sExtends": "pdf", "mColumns": "visible"},
									{ "sExtends": "print", "mColumns": "visible","sMessage": printMessage + " "}
								],
							 "sRowSelect": "multi"
						},
						aaSorting : [],
							
						fnInitComplete: function ( oSettings, json ) {
							//var otb = $(".DTTT_container")
							//alert("fnInitComplete");
							$(".DTTT_container").append($(".dt-button"));
							
							//get sum of amount in column at footer by class name sum
							this.api().columns('.sum').every(function(){
							var column = this;
							var total = 0;
							var sum = column
								.data()
								.reduce(function (a, b) {
									if(a.length == 0)
									{
										a = '0.00';
									} 
									if(b.length == 0)
									{
										b = '0.00';
									}
									var val1 = parseFloat( String(a).replace(/,/g,'') ).toFixed(2);
									var val2 = parseFloat(String(b).replace(/,/g,'') ).toFixed(2);
									total = parseFloat(parseFloat(val1)+parseFloat(val2));
									return  total;
								});
						$(column.footer()).html(format(sum,2));
						});
						
						}
						
					} );	
				
		}
	
	} );		
	</script>
	
	
<?php include_once "includes/foot.php"; ?>
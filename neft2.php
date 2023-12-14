<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - NEFT Payment Options</title>
</head>
<?php include_once "ses_set_as.php"; 
include_once("classes/include/dbop.class.php");
include_once("classes/dbconst.class.php");
include_once("classes/neft.class.php");
include_once("classes/utility.class.php");
//include_once("header.php");
include_once("includes/head_s.php");
include_once("classes/ChequeDetails.class.php");
//include_once("RightPanel.php");
$obj_neft = new neft($m_dbConn);
$obj_utility = new utility($m_dbConn);
$obj_Details = new ChequeDetails($m_dbConn);
$bank_details=$obj_Details->getPayerBankDetails($_SESSION['unit_id']);
$TotalDueAmount=$obj_utility->getDueAmount($_SESSION['unit_id']);
$getPaymentMetod= $obj_utility -> getPaymentOption();
$_REQUEST['BankName']= $bank_details[0]['Payer_Bank'];
$_REQUEST['BranchName']=$bank_details[0]['Payer_Cheque_Branch'];
$_REQUEST['Date']=date('d-m-Y');
$payUrl =  $getPaymentMetod[0]['Paytm_Link'];
?>
 

<html>
<head>
	<!--<link rel="stylesheet" type="text/css" href="css/pagination.css" >-->
   	
	
	<script type="text/javascript" src="js/ajax.js"></script>
   	<script tytpe="text/javascript" src="js/ajax_new.js"></script>
    <script type="text/javascript" src="js/neft.js"></script>
    <script type="text/javascript" src="js/validate.js"></script>
    <!--<link rel="stylesheet" href="css/ui.datepicker.css" type="text/css" media="screen" />-->
    <!--<link rel="stylesheet" type="text/css" href="css/datepicker/jquery.datepick.css"> 
    <script type="text/javascript" src="js/datepicker/jquery.min.js"></script>
    <script type="text/javascript" src="js/datepicker/jquery.plugin.js"></script> 
	<script type="text/javascript" src="js/datepicker/jquery.datepick.js"></script>-->
    
	<!--<script type="text/javascript" src="javascript/jquery-1.2.6.pack.js"></script>-->
    <!--<script type="text/javascript" src="js/jquery-migrate-1.2.1.js"></script>-->
    <!--<script type="text/javascript" src="javascript/jquery.clockpick.1.2.4.js"></script>
    <script type="text/javascript" src="javascript/ui.core.js"></script>
    <script type="text/javascript" src="javascript/ui.datepicker.js"></script>-->
    
	<script language="javascript" type="application/javascript">
	$(function()
        {
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker({ 
            dateFormat: "dd-mm-yy", 
            showOn: "both", 
            buttonImage: "images/calendar.gif", 
	    minDate: new Date(),
	    maxDate: maxGlobalCurrentYearEndDate,
            buttonImageOnly: true,
			yearRange: "-1:+0"
			 })
		
		});
	
	function Pay()
	{
		//alert("PAY");
		//window.location.href = "http://m.p-y.tm/UniAu";
		//window.location.href = "Pay.php";
		/*var unitID = <?php //echo $_SESSION['unit_id'] ?>;
		var BillType = 0;
		var amount = 10000.00;
		var Email = "rohit561120@gmail.com";
		var MobileNo = "9762688064";
		window.location.href = "MakePayment.php?&UID="+unitID+"&bt="+BillType+"&amt="+amount+"&Email="+Email+"&mob="+MobileNo;*/
	}
	function go_error()
    {
		document.getElementById('error').style.display = 'block';
        setTimeout('hide_error()',6000);	
    }
    function hide_error()
    {
		document.getElementById("error").innerHTML = "";
	 }
	</script>
</head>

<?php if(isset($_REQUEST['ShowData'])){ ?>
<body onLoad="go_error();">
<?php }else{ ?>
<?php } ?>
<body>
<br>	

<div class="panel panel-info" style="margin-top:6%;margin-left:3.5%; border:none;width:70%">
    <div class="panel-heading" id="pageheader">Payment Options</div>
<br>

<?php
//$star = "<font color='#FF0000'>*</font>";
//$redURL = $_REQUEST["SID"];
//$DecodeURL = base64_decode($redURL);
//strstr($DecodeURL, "&SID=");
//$pos = strrpos($DecodeURL, "SID=",-1);
//$URLSocietyID = substr($DecodeURL, $pos);
//echo "SID:".$URLSocietyID;
//echo "SocID:".$_SESSION["society_id"];
/*if($_SESSION["society_id"] == $URLSocietyID)
{
	
}
else
{
	?>
	<script>
	alert('Invalid URL !');
	
	window.location.href = 'initialize.php';
	</script>
    <?php
}*/

$UIDUrl = $_REQUEST['UID'];
$DecodeUIDURL = base64_decode($UIDUrl);
$UIDPos = strpos($DecodeUIDURL, "UID=",-1);
$URLUnitID = substr($DecodeUIDURL, $UIDPos);

/*if($_SESSION['unit_id'] == $URLUnitID)
{	
}
else
{
	?>
	<script>		
	window.location.href = 'logout.php';
	</script>
    <?php	
}*/
?>
<div id="new_entry">
<center>
<form name="startup" name="startup" id="startup" method="startup" style="visibility: visible;margin-top: 50px">
    <div class="col-lg-12" style="">
        <div class="panel panel-success" style="font-size: large;cursor:pointer">
            <div class="panel-heading">
            <table style="width: 90%"><tr><td>
            <i class="fa fa-check-circle fa-5x" style="font-size:10px;font-size:3.75vw"></i>
            </td>

            <td style="margin-bottom:none">
                <p style="margin-left:5%;text-align:justify;padding-bottom:1px">Note : <b>Convenietly pay your bill using PayTM payment gateway. No transaction fee on UPI transactions (Promotional offer).</p>
                </td></tr>
                <tr>
                	<td>
                	</td>
                	<td> 
                	<button name="PayNow" id="PayNow" value="" Onclick="window.open('<?php echo $payUrl?>','_blank');" style="box-shadow: 1px 1px 4px #666;-webkit-border-radius: 5px;padding: 2px 4px 2px 4px;background-color: white;margin-left:5%;width: 30%;height: 45px;">Pay <i class="fa fa-rupee" style="font-size:15px;vertical-align: middle;"></i> <?php echo $TotalDueAmount ?></button> <font style="font-size: 12px;vertical-align: bottom;">powered by</font> <img style="width: 60px;background-color: white;vertical-align: bottom;" src="images/paytmlogo.png"/></td>
                </tr></table>
            </div>
        </div>
        <div class="panel panel-info" style="font-size: large;">
            <div class="panel-heading">
            <table  style="width: 90%">
            <tr><td>
            <i class="fa fa-info-circle fa-5x" style="font-size:10px;font-size:3.75vw"></i>
            </td>
            <td style="margin-bottom:none">
                <p style="margin-left:5%;text-align:justify;padding-bottom:1px">Free of charge. Just Need to enter NEFT transaction details.</p>
                </td></tr>
                <tr>
                <td></td>
                <td><input type="button" name="options" id="options" style="padding: 2px 4px 2px 4px;background-color: white;
    margin-left: 4%;width: 35%;height: 45px;" value="Make Payment" onClick="ShowForm()"  ></td>
                </tr></table>
            </div>
        </div>
  </div>
</form>

<form name="neft" id="neft" method="post" action="ajax/ajaxneft.php" onSubmit="return val();"  style="visibility: hidden;">

<input type="hidden" name="society_id" id="society_id" value="<?php echo DEFAULT_SOCIETY; ?>" />

    <div class="col-lg-12" style="">
        <div class="panel panel-success" style="font-size: large;cursor:pointer" onClick="Pay();">
            <div class="panel-heading">
            <table style="width: 90%"><tr>
            <td>
            <i class="fa fa-check-circle fa-5x" style="font-size:10px;font-size:3.75vw"></i>
            </td>

            <td style="margin-bottom:none">
                <p style="margin-left:5%;text-align:justify;padding-bottom:1px">Note : <b>Convenietly pay your bill using PayTM payment gateway. No transaction fees (Promotional offer). </p>
                </td></tr>
                <tr>
                	<td>
                	</td>
                	<td> 
                	<button name="PayNow" id="PayNow" value=""  Onclick="window.open('<?php $payUrl?>', '_blank');" style="box-shadow: 1px 1px 4px #666;    -webkit-border-radius: 5px;    padding: 2px 4px 2px 4px;background-color: white;margin-left:5%;width: 30%;    height: 45px;">Pay <i class="fa fa-rupee" style="font-size:15px;vertical-align: middle;"></i> <?php echo $TotalDueAmount ?></button> <font style="font-size: 12px;vertical-align: bottom;">powered by</font> <img style="width: 100px;background-color: white;vertical-align: bottom;" src="images/paytmlogo.png"/></td>
                </tr></table>
            </div>
        </div>
        <div class="panel panel-danger" style="font-size: large;">
            <div class="panel-heading">
            <table id="neftrecord" name="neftrecord"><tr><td>
            <i class="fa fa-info-circle fa-5x" style="font-size:10px;font-size:3.75vw"></i>
            </td>
            <td style="margin-bottom:none">
                <p style="margin-left:5%;text-align:justify;padding-bottom:1px">Please note : <b>This is not a payment gateway.</b> Please make NEFT/IMPS payment from your Bank/NetBanking to society's bank account and get the UTR transaction ID of the transaction. After that, please record your NEFT/IMPS transaction here along with transaction ID you received from your bank. 
Your payments recorded here will be reflected in your ledger but it is subject to clearance/reconciliation.</p>
                </td></tr>
                <tr>
                	<td></td>
                </tr></table>
            </div>
        </div>
  </div>
                <!-- /.col-lg-12 -->

<table align='center' style="width:57%;" id="neftdetails">
<?php
	if(isset($_POST['ShowData']))
	{
?>
		<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error'><?php echo $_POST['ShowData']; ?></b></font></td><td></td></tr>
<?php
	}
	else
	{
	?>	
			<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"></b></font></td><td></td></tr>
	<?php
	}
?>
       	<tr align="left">
        	<!--<th><?php echo $star;?>Paid By : &nbsp;</th>-->
			<td colspan="2">               
              <!--  <input type="text" name="ledger_name" value="<?php //echo $_SESSION['name']; ?>" readonly /> -->
                <input type="hidden" name="PaidBy" id="PaidBy" value="<?php echo $_SESSION['unit_id']; ?>" />
                <input type="hidden" name="url" id="url" value="<?php echo 'SID='.$_REQUEST["SID"].'&UID='.$_REQUEST['UID']; ?>" />
                 <input type="hidden" name="tra_type" id="tra_type" value="1">
            </td>
		</tr>
       
       <!-- <tr align="left">
        	<th><?php// echo $star;?>Transaction Type </th>
            <td> &nbsp; : &nbsp; </td>
			<td style="padding-bottom:8px;">
            	<select name="tra_type" id="tra_type" style="width:200px;" onChange="setDetails(this.value);" >
                	<option value="1"> NEFT / IMPS </option>
                    <!--<option value="2"> Cheque </option>-->                	
			<!--	</select>
            </td>
		</tr>-->
        
        <tr align="left">
        	<th><?php echo $star;?>Society Account Name </th>
            <td> &nbsp; : &nbsp; </td>
			<td style="padding-bottom:8px;">
            	<!--<select name="PaidTo" id="PaidTo" style="width:200px;" onfocus='UpdateBanks()'> -->
              <select name="PaidTo" id="PaidTo" style="width:200px;" onChange="GetAccountDetails(this.value)" >
                	<?php echo $ledgercombo = $obj_neft->comboboxEx("select led.id, led.ledger_name from `ledger` AS led JOIN `bank_master` AS bm ON led.id = bm.BankID  where led.categoryid='" . BANK_ACCOUNT . "' AND bm.AllowNEFT=1"); ?>
				</select>
               
            </td>
          
		</tr>
        <tr align="left" style="display:none" id="BankAC">
        	<th id="BankACHeading">Account Number</th>
            <td> &nbsp; : &nbsp;</td>
			<td style="padding-bottom:8px;">
            	<!--<select name="PaidTo" id="PaidTo" style="width:200px;" onfocus='UpdateBanks()'> -->
                <label id="lblAccountNo" name="lblAccountNo" ></label>
                
            </td>
		</tr>
        <tr align="left"  style="display:none" id="BankIFSC">
        	<th id="BankIFSCHeading">IFSC Code</th>
            <td>  &nbsp; : &nbsp; </td>
			<td style="padding-bottom:8px;">
            	<!--<select name="PaidTo" id="PaidTo" style="width:200px;" onfocus='UpdateBanks()'> -->
                <label id="lblIFSCCode" name="lblIFSCCode"></label>
                
            </td>
		</tr>
        
        <tr>
        
			<th id="tra_amt"><?php echo $star;?>Transaction Amount </th>
            <td> &nbsp; : &nbsp; </td>
			<td style="padding-bottom:8px;"><input type="text" name="Amount" id="Amount" onKeyPress="return blockNonNumbers(this, event, true, false);" /></td>
		</tr>
        <script>
       $('#Amount').on("paste",function(e) {  
        var $this = $(this);
        setTimeout(function(){
            var val = $this.val(), 
                regex = /^[\d]+$/;
				var s=val.replace(/[^\d.-]/g, ''); 
				//alert(s);
				$this.val( s );   
        },0);

});
</script>
        <tr>
			<th id="tra_date"><?php echo $star;?>Transaction Date </th>
            <td> &nbsp; : &nbsp; </td>
			<td style="padding-bottom:8px;"><input type="text" name="Date" id="Date"  value="<?php echo date('d-m-Y');?>"  class="basics" size="10" readonly  style="width:80px;" onChange="ajaxValidateDate(false,this.value);" /></td>
		</tr>
        
        <tr>
        	<!--<th><?php echo $star;?>Voucher Date : &nbsp;</th> -->
            <td colspan="2"><input type="hidden" name="voucherDate" id="voucherDate"  readonly />
            </td>
        </tr>
        <tr>
        	<th id="tra_no"><?php echo $star;?>Transaction ID </th>
            <td> &nbsp; : &nbsp; </td>
            <td style="padding-bottom:8px;"><input type="text" name="TransationNo" id="TransationNo"  onkeypress="return checkAlphaNumeric(event);" /></td>
        </tr>
         <script>
       $('#TransationNo').on("paste",function(e) {  
        var $this = $(this);
        setTimeout(function(){
            var val = $this.val(), 
                regex = /^[\d]+$/;
				var s=val.replace(/[^a-z0-9#-]/gi, ''); 
				//alert(s);
				$this.val( s );   
        },0);

});
</script>
        <tr align="left">
        	<th >Payment For</th>
            <td>&nbsp; : &nbsp;</td>
			<td style="padding-bottom:8px;"><select name="BillType" id="BillType" value="<?php echo $_REQUEST['BillType'];?>">
            		<OPTION VALUE="<?php echo BILL_TYPE_REGULAR; ?>">Maintenance Bill</OPTION>
                    <OPTION VALUE="<?php echo BILL_TYPE_SUPPLEMENTARY; ?>" >Supplementary Bill</OPTION>
                </select>
            </td>
		</tr>
        <tr>
			<th><?php echo $star;?>Payer Bank Name </th>
            <td> &nbsp; : &nbsp; </td>
			<td style="padding-bottom:8px;"><input type="text" name="BankName" id="BankName" /></td>
		</tr>
		        
        <tr>
			<th><?php //echo $star;?>Payer Branch Name </th>
            <td> &nbsp; : &nbsp; </td>
			<td style="padding-bottom:8px;"><input type="text" name="BranchName" id="BranchName"/></td>
		</tr>
         <tr>
        	<th><?php //echo $star;?>Comments </th>
            <td> &nbsp; : &nbsp; </td>
            <td style="padding-bottom:8px;"><textarea name="Comments" id="Comments" style="width:200px;"></textarea></td>                      
        </tr>
       <!-- <tr><td> </td></tr><tr><td></td></tr><tr><td></td></tr> -->
        <tr>
        	<td><input type="hidden" name="id" id="id"></td>
			<td colspan="2" align="center" style="padding-bottom:8px;">   
            	<input type="hidden" name="mode" id="mode">         	
                <input type="submit" name="insert" id="insert" value="Submit" style="background-color:#337AB7;width:80px; height:32px;color:#FFF;border-color:#2E6DA4;" >&nbsp;
                <input type="button" name="cancel" id="cancel" value="Cancel" style="background-color:#337AB7;width:80px; height:32px;color:#FFF;border-color:#2E6DA4;"  >
            </td>
		</tr>
</table>
</form>

<script>
	$( document ).ready(function() {
	document.getElementById("view_entry").style.visibility = "hidden";
});

	<!--document.getElementById('PaidBy').value = '<?php //echo $_REQUEST['PaidBy']; ?>'; -->
	document.getElementById('PaidTo').value = '<?php echo $_REQUEST['PaidTo']; ?>';
	document.getElementById('BankName').value = '<?php echo $_REQUEST['BankName']; ?>';
	document.getElementById('BranchName').value = '<?php echo $_REQUEST['BranchName']; ?>';
	document.getElementById('Amount').value = '<?php echo $_REQUEST['Amount']; ?>';
	document.getElementById('Date').value = '<?php echo $_REQUEST['Date']; ?>';
	<!--document.getElementById('voucherDate').value = '<?php echo $_REQUEST['voucherDate']; ?>'; -->
	document.getElementById('TransationNo').value = '<?php echo $_REQUEST['TransationNo']; ?>';
	document.getElementById('Comments').value = '<?php echo $_REQUEST['Comments']; ?>';
	
	
	<?php 
		if(isset($_REQUEST['id']) && isset($_REQUEST['insert']) && ($_REQUEST['insert'] == 'Update' || $_REQUEST['insert'] == 'Approve'))
		{
	?>
		document.getElementById('id').value = '<?php echo $_REQUEST['id']; ?>';
		document.getElementById("insert").value="<?php echo $_REQUEST['insert']; ?>";
	<?php
		}
	?>
	function GetAccountDetails1(SelectedID)
	{
		console.log(document.getElementById('tra_type').value);
		//if(document.getElementById('PaidTo').value  == "1")
		//{
			document.getElementById('PaidTo').innerHTML = "<?php echo $ledgercombo = $obj_neft->comboboxEx("select led.id, CONCAT(bm.AcNumber,' - ', led.ledger_name) from `ledger` AS led JOIN `bank_master` AS bm ON led.id = bm.BankID  where led.categoryid='" . BANK_ACCOUNT . "' AND bm.AllowNEFT=1"); ?>";
		//}
		//else if(document.getElementById('tra_type').value  == "2")
		//{
			document.getElementById('PaidTo').innerHTML = "<?php echo $ledgercombo = $obj_neft->comboboxEx("select led.id, CONCAT(bm.AcNumber,' - ', led.ledger_name) from `ledger` AS led JOIN `bank_master` AS bm ON led.id = bm.BankID  where led.categoryid='" . BANK_ACCOUNT . "'"); ?>";
		//}
	}
	function scrollIntoView(eleID) 
	{
	   var e = document.getElementById(eleID);
	   if (!!e && e.scrollIntoView) {
	       e.scrollIntoView();
	   }
	}
	$("#cancel").click(function() {
		//$(window).scrollTop(0);
		$('html, body').animate({
        scrollTop: $("#startup").offset().top
    }, 2000);
	});

	$("#options").click(function() {
    $('html, body').animate({
        scrollTop: $("#neftrecord").offset().top
    }, 2000);
});
	function ShowForm()
	{
		document.getElementById("startup").style.display = "none";
		document.getElementById("startup").style.visibility = "hidden";
		//document.getElementById("neftdetails").scrollIntoView();
		document.getElementById("neft").style.visibility = "visible";
		document.getElementById("view_entry").style.visibility = "visible";
		//window.scroll(0,findPos(document.getElementById("neftdetails")));
	}
	function findPos(obj) 
	{
	    var curtop = 0;
	    if (obj.offsetParent) {
	        do {
	            curtop += obj.offsetTop;
	        } while (obj = obj.offsetParent);
	    return [curtop];
	    }
	}
	function setDetails(type)
	{				
		if(type == 2)
		{
			document.getElementById('tra_amt').innerHTML = "<?php echo $star;?>Cheque Amount";
			document.getElementById('tra_date').innerHTML = "<?php echo $star;?>Cheque Date";
			document.getElementById('tra_no').innerHTML = "<?php echo $star;?>Cheque Number";			
			document.getElementById('TransationNo').removeAttribute('onkeypress');
			//document.getElementById('TransationNo').onkeypress ="return blockNonNumbers(this, event, false, false)";
			document.getElementById('TransationNo').setAttribute('onkeypress',"return blockNonNumbers(this, event, false, false)");
		
		}
		else
		{
			document.getElementById('tra_amt').innerHTML = "<?php echo $star;?>Transaction Amount";
			document.getElementById('tra_date').innerHTML = "<?php echo $star;?>Transaction Date";
			document.getElementById('tra_no').innerHTML = "<?php echo $star;?>Transaction ID";
			document.getElementById('TransationNo').removeAttribute('onkeypress');
			document.getElementById('TransationNo').setAttribute('onkeypress',"return checkAlphaNumeric(event)");
			//document.getElementById('TransationNo').onkeypress ="return checkAlphaNumeric(event)";
		}
		
	}
	
	var lengthDetails= document.getElementById('PaidTo').length;
	if(lengthDetails >1)
	{
	document.getElementById('PaidTo').disabled=false;
	}
	else
	{
	document.getElementById('PaidTo').disabled=true;
	document.getElementById('PaidTo').style="background-color:#E8E8E8";
	}
	document.getElementById('PaidTo').selectedIndex = "0";
	SelectedID=document.getElementById('PaidTo').value;
	 GetAccountDetails(SelectedID);
</script>

</center>
</div>
<div id="view_entry">
<center>
<table align="center">
<tr>
<td>
<?php
//echo "<br><br><br><br><br><br>";

echo "<br>";
echo $str1 = $obj_neft->pgnation_neft($_SESSION['unit_id']);
/*echo "<br>";
echo $str = $obj_neft->display1($str1);
echo "<br>";
$str1 = $obj_neft->pgnation();
echo "<br>";*/
?>
</td>
</tr>
</table>
</center>
</div>
<?php
if(IsReadonlyPage() == true)
{?>
<script>
	$("#neft").css( 'display', 'none' );
	$("#Amount").bind('paste', function(e) {
    $(this).attr('value') = $.trim(this).val();
    alert($(this).val()); //why no alert?
});
</script>
<?php }?>

</div>
<?php include_once "includes/foot.php"; ?>
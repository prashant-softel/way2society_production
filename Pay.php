<?php include_once "ses_set_as.php"; 
include_once("classes/include/dbop.class.php");
include_once("classes/dbconst.class.php");
include_once("classes/neft.class.php");
include_once("classes/utility.class.php");
//include_once("header.php");
include_once("includes/head_s.php");
include_once("classes/ChequeDetails.class.php");
include_once("classes/view_member_profile.class.php");

//include_once("RightPanel.php");
//dbop objRoot = new dbop(true);
$obj_neft = new neft($m_dbConn);
$m_dbConnRoot = new dbop(true);
$obj_utility = new utility($m_dbConn,$m_dbConnRoot);
//print_r($_SESSION);
//$_SESSION["login_id"];
$DueDetails= $obj_utility->getDueAmountByBillType($_SESSION['unit_id'],0);
$LoginDetails= $obj_utility->GetLoginDetails();
//print_r($LoginDetails);
$obj_Details = new ChequeDetails($m_dbConn);
$bank_details=$obj_Details->getPayerBankDetails($_SESSION['unit_id']);
$PGBeneficiaryBank = $obj_utility->GetPaymentGatewayBankID();
//echo "bank:".$PGBeneficiaryBank;
$obj_view_member_profile = new view_member_profile($m_dbConn);
//$TotalDueAmount=$obj_utility->getDueAmount($_SESSION['unit_id']);
//print_r($obj_view_member_profile);
//print_r($_SESSION);
$show_member_main  = $obj_view_member_profile->show_member_main_by_OwnerID();
$sql = "select led.id, led.ledger_name from `ledger` AS led JOIN `bank_master` AS bm ON led.id = bm.BankID  where led.categoryid='" . BANK_ACCOUNT . "' AND bm.AllowNEFT=1";
$result= $m_dbConn->select($sql);
//print_r($result);
$PaidTo = $result[0]["id"];
//echo "PaidTo:".$PaidTo;
//echo "test".print_r($show_member_main);
//$OwnerName = $show_member_main[0]['owner_name'];
//$ContactNo = $show_member_main[0]['mob'];
//$Email = $show_member_main[0]['email'];
$Email = $LoginDetails[0]["member_id"];
$OwnerName = $LoginDetails[0]['name'];
$ContactNo = $LoginDetails[0]['mobile_number'];
$UnitID = $_SESSION["unit_id"];
$LoginID = $_SESSION["login_id"]; 
$BT= 0;
//$w2surl = "localhost://beta_aws_3/";
$w2surl = "https://way2society.com/";
$SuccessURL = $w2surl."success.php?LID=".$LoginID."&UID=".$UnitID."&BT=".$BT."&amt=".$Amount."&Email=".$Email."&mob=".$ContactNo ."&PT=".$PaidTo ;
$FailureURL = $w2surl."failure.php?LID=".$LoginID."&UID=".$UnitID."&BT=".$BT."&amt=".$Amount."&Email=".$Email."&mob=".$ContactNo ."&PT=".$PaidTo ;
//$FailureURL = "localhost://beta_aws_4/failure.php?&UID=".$UnitID."&BT=".$BT."&amt=".$Amount."&Email=".$Email."&mob=".$MobileNo;
//$ContactNo = "9762688064";
//echo "<br>owner:".$OwnerName." Contact: ". $ContactNo . " Email: ".$Email ;
//echo $bank_details[0]['Payer_Cheque_Branch'];$_REQUEST['BankName'];
$_REQUEST['BankName']= $bank_details[0]['Payer_Bank'];
$_REQUEST['BranchName']=$bank_details[0]['Payer_Cheque_Branch'];
$_REQUEST['Date']=date('d-m-Y');
//echo "date". Date('Y-m-d');
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
            buttonImageOnly: true,
			yearRange: "-1:+0"
			 })
		
		});
	
	function Pay()
	{
		
		var unitID = <?php echo $_SESSION['unit_id'] ?>;
		var BillType = 0;
		var amount = 10000.00;
		var Email = "rohit561120@gmail.com";
		var MobileNo = "9762688064";
		window.location.href = "MakePayment.php?&UID="+unitID+"&bt="+BillType+"&amt="+amount+"&Email="+Email+"&mob="+MobileNo;
	}
	$( document ).ready(function() {
       document.getElementById('PaidTo').selectedIndex = "0";
       //alert("test");
       setamount(0);
    });
	
	function go_error()
    {
		document.getElementById('error').style.display = 'block';
        setTimeout('hide_error()',6000);	
    }
    function hide_error()
    {
		document.getElementById("error").innerHTML = "";
	 }
	function setamount(BillType)
	{
			console.log(BillType);
			var UnitID = <?php echo $_SESSION["unit_id"]; ?>;
			 $.ajax({
	          url: 'ajax/ajaxPaymentGateway.php',
	              type: 'POST',
	              data: { "method":"getDueAmount", "UnitID": UnitID, "BillType": BillType},
	              success: function(data){
	                var fetchdata = data;
	                if(fetchdata == "")
	                {
	                	fetchdata = "0.00";
	                }
	                //console.log(JSON.stringify(fetchdata));
	                //fetchdata = JSON.parse(fetchdata);
	               //alert(fetchdata);
	               document.getElementById("amount").value = fetchdata;
		 		   document.getElementById("insert").innerHTML = "Pay " +document.getElementById("amount").value;
		 		   if(fetchdata == "0.00")
	                {
	                	//alert("disabled");
	                	document.getElementById("insert").disabled = true;
	                }
	                else
	                {
	                	document.getElementById("insert").disabled = false;
	                }
	          //VoucherData = fetchdata[1].split("#");              
	          //window.open('print_voucher.php?&vno=' + VoucherData[0] + '&type=' + VoucherData[1]);
	          }
	        });
	}
	//window.location.href = 'logout.php';
	</script>
</head>

<?php if(isset($_REQUEST['ShowData'])){ ?>
<body onLoad="go_error();">
<?php }else{ ?>
<?php } ?>
<body>
<br>	

<div class="panel panel-info" style="margin-top:6%;margin-left:3.5%; border:none;width:70%">
    <div class="panel-heading" id="pageheader">Submit Your NEFT/IMPS Transaction Details</div>
<br>

<?php
$star = "<font color='#FF0000'>*</font>";
$redURL = $_REQUEST["SID"];
$DecodeURL = base64_decode($redURL);
//strstr($DecodeURL, "&SID=");
$pos = strrpos($DecodeURL, "SID=",-1);
$URLSocietyID = substr($DecodeURL, $pos);
//echo "SID:".$URLSocietyID;
//print_r($_SESSION);
//print_r($show_member_main);
if($_SESSION["society_id"] == $URLSocietyID)
{
}
else
{
	?>
	<script>
	//alert('Invalid URL !');
	
	//window.location.href = 'initialize.php';
	</script>
    <?php
}

$UIDUrl = $_REQUEST['UID'];
$DecodeUIDURL = base64_decode($UIDUrl);
$UIDPos = strpos($DecodeUIDURL, "UID=",-1);
$URLUnitID = substr($DecodeUIDURL, $UIDPos);

if($_SESSION['unit_id'] == $URLUnitID)
{	
}
else
{
	?>
    <?php	
}
?>

	
<div id="new_entry">
<center>
<form name="neft" id="neft" method="post" action="MakePayment.php" onSubmit="return val();">
<input type="hidden" name="society_id" id="society_id" value="<?php echo DEFAULT_SOCIETY; ?>" />

    <div class="col-lg-12" style="">
        <div class="panel panel-info" style="font-size: large;">
            <div class="panel-heading">
            <table style="width: 90%">
            	<tr>
            		<td style="width: 20%">
		            <i class="fa fa-info-circle fa-5x" style="font-size:10px;font-size:3.75vw"></i>
		            </td>
		            <td style="margin-bottom:none">
		                <p style="text-align:justify;padding-bottom:1px;padding-left: 2%"><b> Note: 2% Payment Gateway charges extra.</p>
		            </td>
            	</tr>
            </table>
            </div>
        </div>
  </div>
                <!-- /.col-lg-12 -->

<table align='center' style="width:57%;">
<?php
	if(isset($_POST['ShowData']))
	{
?>
		<tr height='30'>
			<td colspan='4' align='center'><font color='red' size='-1'><b id='error'><?php echo $_POST['ShowData']; ?></b></font></td>
			<td></td>
		</tr>
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
        	<th ><?php echo $star;?>Payment For</th>
            <td>&nbsp; : &nbsp;</td>
			<td style="padding-bottom:8px;"><select name="BillType" id="BillType" onchange="setamount(this.value)"> value="<?php echo $_REQUEST['BillType'];?>">
            		<OPTION VALUE="<?php echo BILL_TYPE_REGULAR; ?>">Maintenance Bill</OPTION>
                    <OPTION VALUE="<?php echo BILL_TYPE_SUPPLEMENTARY; ?>" >Supplementary Bill</OPTION>
                </select>
            </td>
		</tr>
        <tr>
        
			<th id="tra_amt"><?php echo $star;?>Transaction Amount </th>
            <td> &nbsp; : &nbsp; </td>
			<td style="padding-bottom:8px;"><input type="text" name="amount" id="amount" value="<?php echo $DueDetails[0]["TotalBillPayable"] ?>" required onKeyPress="return blockNonNumbers(this, event, true, false);" /></td>
		</tr>
		<tr>
        
			<th id="tra_amt"><?php echo $star;?>Name</th>
            <td> &nbsp; : &nbsp; </td>
			<td style="padding-bottom:8px;"><input type="text" name="firstname" id="firstname" required value="<?php echo $OwnerName ?>" /></td>
		</tr>
		<tr>
        
			<th id="tra_amt"><?php echo $star;?>Email </th>
            <td> &nbsp; : &nbsp; </td>
			<td style="padding-bottom:8px;"><input type="text" name="email" id="email" required value="<?php echo $Email ?>" /></td>
		</tr>
		<tr>
        
			<th id="tra_amt"><?php echo $star;?>Mobile No </th>
            <td> &nbsp; : &nbsp; </td>
			<td style="padding-bottom:8px;"><input type="text" name="phone" id="phone" required value="<?php echo $ContactNo ?>" /></td>
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
        	<!--<th><?php echo $star;?>Voucher Date : &nbsp;</th> -->
            <td colspan="2"><input type="hidden" name="voucherDate" id="voucherDate"  readonly />
            </td>
        </tr>
       
         <tr>
        	<th><?php echo $star;?>Comments </th>
            <td> &nbsp; : &nbsp; </td>
            <td style="padding-bottom:8px;"><textarea name="productinfo" id="productinfo" required style="width:200px;"></textarea></td>                      
        </tr>
       <!-- <tr><td> </td></tr><tr><td></td></tr><tr><td></td></tr> -->
        <tr>
        	<td><input type="hidden" name="id" id="id"></td>
			<td colspan="2" align="center" style="padding-bottom:8px;">
			</td>
			</tr>
			<tr>
			<td colspan="3">   
            	<input type="hidden" name="mode" id="mode">         	
                <button type="submit" class="btn btn-success" name="insert" id="insert" value="" style="box-shadow: 1px 1px 4px #666;    -webkit-border-radius: 5px;    padding: 2px 4px 2px 4px;margin-left:5%;width: 50%;    height: 45px;font-size:20px;background-color: green !important;color: white">
                	<!-- Pay <i class="fa fa-rupee" style="font-size:20px;vertical-align: middle;"></i><?php //echo $TotalDueAmount; ?>  -->
                </button>
                <font style="font-size: 12px;vertical-align: bottom;">powered by</font> 
                <img style="width: 100px;vertical-align: bottom;" src="images/payumoney_logo.png"/>
            </td>
                
            </td>
		</tr>
		<tr>
			<td style="padding-bottom:8px;"><input type="hidden" name="surl" id="surl" required value="<?php echo $SuccessURL ?>" /></td>
			<td style="padding-bottom:8px;"><input type="hidden" name="furl" id="furl" required value="<?php echo $FailureURL ?>" /></td>
			<td>
				 <input type="hidden"  name="PaidTo" id="PaidTo" style="width:200px;" value="<?php echo $PGBeneficiaryBank; ?>">
				
			</td>
		</tr>
</table>
</form>

<script>
	function val()
{
	var PaidTo =trim(document.getElementById("PaidTo").value);
	//alert(PaidTo);
	var transaction_amount =trim(document.getElementById("amount").value);
	//alert(transaction_amount);
	var name =trim(document.getElementById("firstname").value);
	//alert(name);
	var email =trim(document.getElementById("email").value);
	//alert(email);
	var phone =trim(document.getElementById("phone").value);
	//alert(phone);
	var bFlag = true;
	var sMsg ="";
	
	if(PaidTo == "" || PaidTo == 0 )
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "No Bank details found for society. Please contact Administrator.";
		go_error();
		return false;
	}
	else if(transaction_amount == "")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter Transaction Amount ";
		document.getElementById("Amount").focus();
		
		go_error();
		return false;
	}
	else if(name == "")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter Your Name ";
		document.getElementById("name").focus();
		
		go_error();
		return false;
	}
	else if(email == "")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter Transaction Amount ";
		document.getElementById("email").focus();
		
		go_error();
		return false;
	}
	else if(phone == "")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter Mobile Number ";
		document.getElementById("phone").focus();
		
		go_error();
		return false;
	}
	else if(phone.length < 10)
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter valid Mobile Number ";
		document.getElementById("phone").focus();
		
		go_error();
		return false;
	}
	$('button[type=submit]').click(function(){
    $(this).attr('disabled', 'disabled');
});
   
//alert("submit false");


	function LTrim( value )
	{
	var re = /\s*((\S+\s*)*)/;
	return value.replace(re, "$1");
	}
	function RTrim( value )
	{
	var re = /((\s*\S+)*)\s*/;
	return value.replace(re, "$1");
	}
	function trim( value )
	{
	return LTrim(RTrim(value));
	}
	
	//alert("ready to send ");
	return true;
}
	<!--document.getElementById('PaidBy').value = '<?php //echo $_REQUEST['PaidBy']; ?>'; -->
	
	
	
	<?php 
		if(isset($_REQUEST['id']) && isset($_REQUEST['insert']) && ($_REQUEST['insert'] == 'Update' || $_REQUEST['insert'] == 'Approve'))
		{
	?>
		document.getElementById('id').value = '<?php echo $_REQUEST['id']; ?>';
		document.getElementById("insert").value="<?php echo $_REQUEST['insert']; ?>";
	<?php
		}
	?>
	
	
	
	
	//var lengthDetails= document.getElementById('PaidTo').length;
	//document.getElementById('PaidTo').selectedIndex = "0";
	//SelectedID=document.getElementById('PaidTo').value;
	// GetAccountDetails(SelectedID);
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
//echo $str1 = $obj_neft->pgnation_neft($_SESSION['unit_id']);
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

</div>
<?php include_once "includes/foot.php"; ?>
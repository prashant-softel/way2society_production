<?php include_once "ses_set_as.php"; 
include_once("classes/include/dbop.class.php");
include_once("classes/dbconst.class.php");
include_once("classes/neft.class.php");
include_once("classes/utility.class.php");
//include_once("header.php");
include_once("dbconst.class.php");
include_once("includes/head_s.php");
include_once("classes/PaymentGateway.class.php");
include_once("classes/ChequeDetails.class.php");
include_once("classes/view_member_profile.class.php");


//include_once("RightPanel.php");
$obj_neft = new neft($m_dbConn);
$TransactionID = $_REQUEST["TrnxID"];
//echo $TransactionID;
$obj_utility = new utility($m_dbConn);
$obj_Details = new ChequeDetails($m_dbConn);
$bank_details=$obj_Details->getPayerBankDetails($_SESSION['unit_id']);
$obj_Payment_Gateway = new PaymentGateway($m_dbConn);
$obj_view_member_profile = new view_member_profile($m_dbConn);
$TotalDueAmount=$obj_utility->getDueAmount($_SESSION['unit_id']);

/*$GatewayID = "1";
$LoginID =  "192";
$PaidBy = "21";
$PaidTo = "92";
$Date = "2017-12-07";
$amount = "180.0";
$BillType = "0";
$txnid = "9cc549c1b276142b8ed8";
$status= "failure";
$unmapstatus = "userCancelled";
$payuMoneyId = "1111497144";
$Mode = "-";
$Comments = "test";*/
//echo "start";
//'169','92','92','2017-12-07','180.0','0','9cc549c1b276142b8ed8','failure','userCancelled','1111497144','-','test'
//print_r($obj_view_member_profile);
//print_r($_SESSION);
//$resp= $obj_Payment_Gateway->CompletePayment($LoginID,$PaidBy,$PaidTo,$Date,$amount,$BillType,$txnid,$status,$unmapstatus,$payuMoneyId,$Mode,$Comments);
//echo "done";
$show_member_main  = $obj_view_member_profile->show_member_main_by_OwnerID();
$sql = "select led.id, led.ledger_name from `ledger` AS led JOIN `bank_master` AS bm ON led.id = bm.BankID  where led.categoryid='" . BANK_ACCOUNT . "' AND bm.AllowNEFT=1";
$result= $m_dbConn->select($sql);
//echo $sql;
$Timestamp = $Response[0]["TimeStamp"];
$date = new DateTime($Timestamp);
$TrnxTimestamp = $date->format('d-m-Y H:i:s');
//print_r($new_date_format);
if($_SESSION["role"] == ROLE_SUPER_ADMIN && isset($_REQUEST["uid"]))
{
	$UnitID = $_REQUEST["uid"];
}
else
{
	$LoginID = $_SESSION["unit_id"];
}
$LoginID = $_SESSION["login_id"]; 

$Response = $obj_Payment_Gateway->GetTransactionDetails($TransactionID, $UnitID);
if(isset($Response))
{
	//print_r($Response);
    $ResponseStatus = $Response[0]["Status"];
    $PaidTo = $result[0]["id"];
	//echo "id:".$Response[0]["GatewayID"];
	if($Response[0]["GatewayID"] == "1")
	{
		$FailReason = $Response[0]["unmappedstatus"];
	}
	else if($Response[0]["GatewayID"] == "2")
	{
		//echo "3";
        if($Response[0]["Status"] == "TXN_SUCCESS")
        {
		    $FailReason = $Response[0]["Comments"];
        }
        else
        {
            $FailReason = $Response[0]["unmappedstatus"];
        }
	}
	
}
else
{
    $ResponseStatus = " Transaction ID <". $TransactionID ."> is Invalid";
    $PaidTo = "-";
    $FailReason = " Transaction ID <". $TransactionID ."> is Invalid";
}
//echo "r:".$FailReason;
//echo "PaidTo:".$PaidTo;
//echo "test".print_r($show_member_main);
$OwnerName = $show_member_main[0]['owner_name'];
$ContactNo = $show_member_main[0]['mob'];
$Email = $show_member_main[0]['email'];
$BT= 0;
//$w2surl = "localhost://beta_aws_3/";
$w2surl = "https://way2society.com/";
$SuccessURL = $w2surl."success.php?&LID=".$LoginID."&UID=".$UnitID."&BT=".$BT."&amt=".$Amount."&Email=".$Email."&mob=".$ContactNo ."&PT=".$PaidTo ;
$FailureURL = $w2surl."failure.php?&LID=".$LoginID."&UID=".$UnitID."&BT=".$BT."&amt=".$Amount."&Email=".$Email."&mob=".$ContactNo ."&PT=".$PaidTo ;
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
    function redirectNow()
    {
        window.location.href = "MaintenanceBill_m.php";
    }
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
	
	$( document ).ready(function() {
       document.getElementById('PaidTo').selectedIndex = "0";
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
	</script>
</head>

<?php if(isset($_REQUEST['ShowData'])){ ?>
<body onLoad="go_error();">
<?php }else{ ?>
<?php } ?>
<body>
<br>	

<div class="panel panel-info" style="margin-top:3%;margin-left:3.5%; border:none;width:70%">
    <div class="panel-heading" id="pageheader">Payment Transaction Status</div>
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
//echo $ResponseStatus;
?>
<div id="new_entry">
<center>
<form name="neft" id="neft" method="post" action="PayU_Pay.php">
<input type="hidden" name="society_id" id="society_id" value="<?php echo DEFAULT_SOCIETY; ?>" />

    <div class="col-lg-12" style="">
    	<?php
        $bShowSummary= false; 
    	if(strtolower($ResponseStatus) == "failure" || strtolower($ResponseStatus) == "txn_failure") 
    	{
    	?>
        <div class="panel panel-danger" style="font-size: large;">
            <div class="panel-heading">
            <table style="width: 90%">
            	<tr>
            		<td style="width: 20%">
		            <i class="fa fa-times-circle fa-5x" style="font-size:10px;font-size:3.75vw"></i>
		            </td>
		            <td style="margin-bottom:none">
                        <table style="width: 100%">
                            <tr>
                                <td style="width: 70%">
        		                <p style="text-align:justify;padding-bottom:1px;padding-left: 2%"><b> Payment Failed. Reason 
        		                	<?php echo $FailReason; ?></b>
                                </p>
                                </td>
                                <td>
                                    <input type="button" value="Retry" style="width: 100px;text-align: center;" onClick="window.location.href='PayU_Pay.php?pg=2'">
                                </td>
        		            </tr>
                        </table>
                    </td>
            	</tr>
            </table>
            </div>
        </div>
    	<?php
        $bShowSummary= true;
        }
        else if(strtolower($ResponseStatus) == "success" || strtolower($ResponseStatus) == "txn_success") 
    	{
			//echo "inside:";
    	?>
    
    	<div class="panel panel-success" style="font-size: large;">
            <div class="panel-heading">
            <table style="width: 90%;" >
            	<tr>
            		<td style="width: 20%">
		            <i class="fa fa-check-circle fa-5x" style="font-size:10px;font-size:3.75vw"></i>
		            </td>
		            <td style="margin-bottom:none">
		                <p style="text-align:justify;padding-bottom:1px;padding-left: 2%"><b> Transaction Completed successfully.

		                	</b></p>
		            </td>
            	</tr>
            </table>
            </div>
        </div>
        <?php
        $bShowSummary= true;
    	}
        else
        {
            ?>
        <div class="panel panel-danger" style="font-size: large;">
            <div class="panel-heading">
                <?php echo $ResponseStatus ?>
            </div>
        </div>
        <?php
        }
    	?>
  <?php 
  if($bShowSummary == true) 
  {
    ?>
  <div class="panel-body" style="font-size: large;">
        
  <table style="width: 80%;box-shadow: 1px 1px 4px #666;
    padding: 2px 4px 2px 4px;">
  	<tr style="width: 100%">
  		
  			<td style="width: 3%"></td><td style="width: 55%;float: right;">Transaction ID</td><td style="width: 2%">:</td><td style="width: 50%"><?php echo $Response[0]["TranxID"] ?></td>
	</tr>
    <tr>
    		<td style="width: 3%"></td><td style="width: 55%;float: right;">Amount</td><td style="width: 2%">:</td><td style="width: 50%"><?php echo $Response[0]["Amount"] ?></td>
	</tr>
    <tr>
    		<td style="width: 3%"></td><td style="width:55%;float: right;">Date</td><td  style="width: 2%">:</td><td style="width: 50%"><?php echo getDisplayFormatDate( $Response[0]["TimeStamp"]) ?></td>
	</tr>
    <tr>
    		<td style="width: 3%"></td><td style="width: 55%;float: right;">Status</td><td  style="width: 2%">:</td><td style="width: 50%"><?php echo $Response[0]["Status"] ?></td>
  		
  	</tr>
    <tr>
            <td style="width: 3%"></td><td style="width: 55%;float: right;">TimeStamp</td><td  style="width: 2%">:</td><td style="width: 50%"><?php echo $TrnxTimestamp ?></td>
        
    </tr>
    <tr>
            <td style="width: 3%"></td><td style="width: 55%;float: right;">Description</td><td  style="width: 2%">:</td><td style="width: 50%"><?php echo $FailReason  ?></td>
        
    </tr>
    <tr><td colspan="2"></td><td colspan="2"><input type="button" value="OK" style="width: 100px;text-align: center;margin-left: -10%;margin-top: 20px" onClick="redirectNow()"></td>
    </tr>
  </table>
  
</div>
<?php 
}
?>
</div>
                <!-- /.col-lg-12 -->

</form>

<script>
	
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
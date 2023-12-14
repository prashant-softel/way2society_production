<?php
echo "
<html>
<head>
<title>
</title>
<body>
</body>
</html>";

try
{
  include_once("classes/include/dbop.class.php");
include_once("classes/dbconst.class.php");
include_once("classes/ChequeDetails.class.php");
include_once("classes/include/fetch_data.php");
include_once("classes/PaymentGateway.class.php");
//echo "incl";
$dbConn = new dbop();
//$obj_neft = new neft($m_dbConn);
$obj_ChequeDetails = new ChequeDetails($dbConn);

$obj_Payment_Gateway = new PaymentGateway($dbConn);
//echo "obj";
$status=$_POST["status"];
    $unmapstatus=$_POST["unmappedstatus"];
$firstname=$_POST["firstname"];
$amount=$_POST["amount"];
$txnid=$_POST["txnid"];
$posted_hash=$_POST["hash"];
$key=$_POST["key"];
$productinfo=$_POST["productinfo"];
$Comments = $productinfo;
$email=$_POST["email"];
$payuMoneyId=$_POST["payuMoneyId"];
$LoginID = $_REQUEST["LID"];
$PaidBy = $_REQUEST["UID"];//its UnitID
$PaidTo = $_REQUEST["PT"];
$BillType = $_REQUEST["BT"];
$Mode = $_REQUEST["mode"];
$BankCode = $_REQUEST["bankcode"];
$bank_ref_num = $_REQUEST["bank_ref_num"];
$amount_split = $_REQUEST["amount_split"];
//$salt="GQs7yium";
//$salt="e5iIg1jwi8";

$salt = $_SESSION["PGSalt"];
$Date = Date('Y-m-d');
//echo "LID:".$LoginID." date:".$Date ." txnid: ". $txnid ." amt:". $amount ." paidby:". $PaidBy ." paidto:". $PaidTo." bankcode:". $BankCode ." none:". "-" ."type:". "-2" ."Comments:". $Comments ."BT:". $_REQUEST['BT'];
If (isset($_POST["additionalCharges"])) 
{
       $additionalCharges=$_POST["additionalCharges"];
        $retHashSeq = $additionalCharges.'|'.$salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
        
}
else 
{	  

        $retHashSeq = $salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;

}
//echo "posted_hash:".$posted_hash;

//echo "hash:".$hash;
	 $hash = hash("sha512", $retHashSeq);
	 
   if ($hash != $posted_hash) 
   {
       echo "Invalid Transaction. Please try again";
	   }
   else 
   {
         	   
        //echo "<h3>Thank You. Your order status is ". $status .".</h3>";
        //echo "<h4>Your Transaction ID for this transaction is ".$txnid.".</h4>";
        //echo "<h4>We have received a payment of Rs. " . $amount . ". Your order will soon be shipped.</h4>";

          // echo "<b>Transaction Completed successfully.<b> Note: This is demo transaction.";
        
    // echo $LoginID."|".$PaidBy."|".$PaidTo."|".$Date."|".$amount."|".$BillType."|".$txnid."|".$status."|".$payuMoneyId."|".$Mode."|".$Comments;

     $BankCode= "PayU-".$BankCode;
    //       echo "<br>".$Date."|". $Date."|". $txnid."|". $amount."|". $PaidBy."|". $PaidTo."|". $BankCode;
  //         echo "<br>".$Comments."|". $BillType;
     
$obj_Payment_Gateway->CompletePayment(1, $LoginID,$PaidBy,$PaidTo,$Date,$amount,$BillType,$txnid,$status,$unmapstatus,$payuMoneyId, $bank_ref_num, $amount_split, '0',$Mode,$Comments);
$obj_ChequeDetails->AddNewValues($Date, $Date, $txnid, $amount, $PaidBy, $PaidTo, $BankCode, "-", DEPOSIT_ONLINE, $Comments, $_REQUEST['BT'],0,0,0,0,1/*,1Payment gateway flag*/);
  //         echo "done1";
     
//echo "done2";   
         
	   }
     $w2surl = "http://localhost//beta_aws_7/";
//$w2surl = "https://way2society.com/";

$URL = "Location: http://localhost//beta_aws_7/Response.php?TrnxID=".$txnid;
//       echo "url:".$URL;
header($URL);
           
}
catch(Exception $ex)
{
  echo "exception:".$ex;
}         
?>	
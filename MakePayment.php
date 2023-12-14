<?php include_once "ses_set_as.php"; 
include_once("classes/include/dbop.class.php");
include_once("classes/dbconst.class.php");
include_once("classes/neft.class.php");

include_once("classes/utility.class.php");
include_once("classes/PaymentGateway.class.php");
// Merchant key here as provided by Payu
$MERCHANT_KEY = "rjQUPktU";

// Merchant Salt as provided by Payu
$SALT = "e5iIg1jwi8";
//$SALT = "GQs7yium";
$SALT = $_SESSION["PGSalt"];
// End point - change to https://secure.payu.in for LIVE mode
$PAYU_BASE_URL = "https://test.payu.in";

$action = 'https://sandboxsecure.payu.in/_payment';
$action = '';
/*$UnitID = $_REQUEST["UID"];
$BT = $_REQUEST["bt"];
$Amount = $_REQUEST["amt"];
$GatewayCharges = $Amount * 0.02;
$PayableAmount = $Amount + $GatewayCharges;
$Email = $_REQUEST["Email"];
$MobileNo = $_REQUEST["mob"];
$name = "rohit shinde";
$comment = "test";
$SuccessURL = "localhost://beta_aws_4/success.php?&UID=".$UnitID."&BT=".$BT."&amt=".$Amount."&Email=".$Email."&mob=".$MobileNo;
$FailureURL = "localhost://beta_aws_4/failure.php?&UID=".$UnitID."&BT=".$BT."&amt=".$Amount."&Email=".$Email."&mob=".$MobileNo;*/

//$$posted['amount']

$m_dbConnRoot = new dbop(true);
$obj_utility = new utility($m_dbConn,$m_dbConnRoot);
$socDetails = $obj_utility->GetPaymentGatewayDetails($_SESSION["society_id"]);
$bPaymentGateway = 0;
if(isset($socDetails))
{
  if(isset($socDetails["PaymentGateway"]) && $socDetails["PaymentGateway"] == "1")
  {
       $bPaymentGateway = 1;
       $PGKey = $socDetails["PGKey"];
       $PGSalt = $socDetails["PGSalt"];
       $_SESSION["PGSalt"] = $PGSalt;
  } 
  else
  {
    $link = 'neft.php?SID='. $_REQUEST["SID"].'&UID='. $_REQUEST["UID"];
    $URL = "Location: ".$link;

    header($URL);

  }
}

$posted = array();
if(!empty($_POST)) {
    //print_r($_POST);
  foreach($_POST as $key => $value) {    
    $posted[$key] = $value; 
	
  }
}

$formError = 0;

if(empty($posted['txnid'])) {
  // Generate random transaction id
  $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
} else {
  $txnid = $posted['txnid'];
}
$hash = '';
// Hash Sequence
$hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
if(empty($posted['hash']) && sizeof($posted) > 0) {
  if(
          empty($posted['key'])
          || empty($posted['txnid'])
          || empty($posted['amount'])
          || empty($posted['firstname'])
          || empty($posted['email'])
          || empty($posted['phone'])
          || empty($posted['productinfo'])
          || empty($posted['surl'])
          || empty($posted['furl'])
		  || empty($posted['service_provider'])
  ) {
    $formError = 1;
  } else {
    //$posted['productinfo'] = json_encode(json_decode('[{"name":"tutionfee","description":"","value":"500","isRequired":"false"},{"name":"developmentfee","description":"monthly tution fee","value":"1500","isRequired":"false"}]'));
	$hashVarsSeq = explode('|', $hashSequence);
    $hash_string = '';	
	foreach($hashVarsSeq as $hash_var) {
      $hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
      $hash_string .= '|';
    }

    $hash_string .= $SALT;

echo "n:";
    $hash = strtolower(hash('sha512', $hash_string));
    $action = $PAYU_BASE_URL . '/_payment';
  }
} elseif(!empty($posted['hash'])) {
  $hash = $posted['hash'];
  $action = $PAYU_BASE_URL . '/_payment';
  echo "n:";
}
//echo "hash:".print_r($hash);
?>
<html>
  <head>
    <script type="text/javascript" src="js/ajax.js"></script>
    
    <script type="text/javascript" src="js/ajax_new.js"></script><script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
<script id="bolt" src="https://sboxcheckout-static.citruspay.com/bolt/run/bolt.min.js" bolt-
color="<color-code>"
 bolt-logo="<image path>"></script>
  <script>

    
    var hash = '<?php echo $hash ?>';
    function submitPayuForm() {
      if(hash == '') {
        return;
      }
      var payuForm = document.forms.payuForm;
      payuForm.submit();
    }
    $( document ).ready(function() {
        PaymentGatewayTransaction();
        document.getElementById("payuForm").submit();
    });
    function PaymentGatewayTransaction()
    {
     
      var UnitID = <?php echo $_SESSION["unit_id"]; ?>;
      var LoginID = <?php echo $_SESSION["login_id"]; ?>;

      var PaidTo = <?php echo $_REQUEST["PaidTo"]; ?>;
      //alert(LoginID);
      var Amount = document.getElementById("amount").value;
      //var BillType = document.getElementById("BT").value;
      var BillType = "0";
      var Date = <?php echo date("Y-m-d"); ?>;
      //alert(Date);
      var comment = document.getElementById("productinfo").value;
      if(Amount.length == 0 )
      {
          $.ajax({
          url: 'ajax/ajaxPaymentGateway.php',
              type: 'POST',
              data: { "method":"initiate", "Amount": Amount, "LoginID": LoginID, "UnitID": UnitID, "PT" : PaidTo,"BillType": BillType, "Comment":comment,'Date' : Date},
              success: function(data){
                var fetchdata = data;
               alert(fetchdata);
          //VoucherData = fetchdata[1].split("#");              
          //window.open('print_voucher.php?&vno=' + VoucherData[0] + '&type=' + VoucherData[1]);
          }
        });
      }

    } 
  </script>
  </head>
  <body onload="submitPayuForm()">
    <h2 style="visibility: hidden;">PayU Form</h2>
    <br/>
    <?php if($formError) { ?>
	
      <span style="color:red;visibility: hidden;">Please fill all mandatory fields.</span>
      <br/>
      <br/>
    <?php } ?>
    <form action="<?php echo $action; ?>" method="post" id="payuForm" name="payuForm" style="visibility: hidden;" >
      <input type="hidden" name="key" value="<?php echo $MERCHANT_KEY ?>" />
      <input type="hidden" name="hash" value="<?php echo $hash ?>"/>
      <input type="hidden" name="txnid" value="<?php echo $txnid ?>" />
      <table>
        <tr>
          <td><b>Mandatory Parameters</b></td>
        </tr>
        <tr>
          <td>Amount: </td>
          <td><input name="amount" id="amount" value="<?php echo (empty($posted['amount'])) ? '' : $posted['amount'] ?>" /></td>
          <td>First Name: </td>
          <td><input name="firstname" id="firstname" value="<?php echo (empty($posted['firstname'])) ? $name : $posted['firstname']; ?>" /></td>
        </tr>
        <tr>
          <td>Email: </td>
          <td><input name="email" id="email" value="<?php echo (empty($posted['email'])) ? '' : $posted['email']; ?>" /></td>
          <td>Phone: </td>
          <td><input name="phone" value="<?php echo (empty($posted['phone'])) ? '' : $posted['phone']; ?>" /></td>
        </tr>
        <tr>
          <td>Product Info: </td>
          <td colspan="3"><textarea id="productinfo" name="productinfo"><?php echo (empty($posted['productinfo'])) ? '' : $posted['productinfo'] ?></textarea></td>
        </tr>
        <tr>
          <td>Success URI: </td>
          <td colspan="3"><input name="surl" value="<?php echo (empty($posted['surl'])) ? '' : $posted['surl'] ?>" size="64" /></td>
        </tr>
        <tr>
          <td>Failure URI: </td>
          <td colspan="3"><input name="furl" value="<?php echo (empty($posted['furl'])) ? '' : $posted['furl'] ?>" size="64" /></td>
        </tr>

        <tr>
          <td colspan="2"><input type="hidden" name="service_provider" value="payu_paisa" size="64" /></td>
<input name="PaidTo" id="PaidTo" value="<?php echo (empty($posted['PaidTo'])) ? '' : $posted['PaidTo']; ?>" />
        </tr>

        <tr>
          <td><b>Optional Parameters</b></td>
        </tr>
        <tr>
          <td>Last Name: </td>
          <td><input name="lastname" id="lastname" value="<?php echo (empty($posted['lastname'])) ? '' : $posted['lastname']; ?>" /></td>
          <td>Cancel URI: </td>
          <td><input name="curl" value="" /></td>
        </tr>
        <tr>
          <td>Address1: </td>
          <td><input name="address1" value="<?php echo (empty($posted['address1'])) ? '' : $posted['address1']; ?>" /></td>
          <td>Address2: </td>
          <td><input name="address2" value="<?php echo (empty($posted['address2'])) ? '' : $posted['address2']; ?>" /></td>
        </tr>
        <tr>
          <td>City: </td>
          <td><input name="city" value="<?php echo (empty($posted['city'])) ? '' : $posted['city']; ?>" /></td>
          <td>State: </td>
          <td><input name="state" value="<?php echo (empty($posted['state'])) ? '' : $posted['state']; ?>" /></td>
        </tr>
        <tr>
          <td>Country: </td>
          <td><input name="country" value="<?php echo (empty($posted['country'])) ? '' : $posted['country']; ?>" /></td>
          <td>Zipcode: </td>
          <td><input name="zipcode" value="<?php echo (empty($posted['zipcode'])) ? '' : $posted['zipcode']; ?>" /></td>
        </tr>
        <tr>
          <td>UDF1: </td>
          <td><input name="udf1" value="<?php echo (empty($posted['udf1'])) ? '' : $posted['udf1']; ?>" /></td>
          <td>UDF2: </td>
          <td><input name="udf2" value="<?php echo (empty($posted['udf2'])) ? '' : $posted['udf2']; ?>" /></td>
        </tr>
        <tr>
          <td>UDF3: </td>
          <td><input name="udf3" value="<?php echo (empty($posted['udf3'])) ? '' : $posted['udf3']; ?>" /></td>
          <td>UDF4: </td>
          <td><input name="udf4" value="<?php echo (empty($posted['udf4'])) ? '' : $posted['udf4']; ?>" /></td>
        </tr>
        <tr>
          <td>UDF5: </td>
          <td><input name="udf5" value="<?php echo (empty($posted['udf5'])) ? '' : $posted['udf5']; ?>" /></td>
          <td>PG: </td>
          <td><input name="pg" value="<?php echo (empty($posted['pg'])) ? '' : $posted['pg']; ?>" /></td>
        </tr>
        <tr>
          <?php if(!$hash) { ?>
            <td colspan="4"><input type="submit" value="Submit" /></td>
          <?php } ?>
        </tr>
      </table>
    </form>
  </body>
</html>

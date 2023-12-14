
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
<title>W2S - Import Payment Receipts</title>
</head>

<?php if(!isset($_SESSION)){ session_start(); } ?>
<?php
 	//Turn off all error reporting
    //error_reporting(0);
	
	
	include_once("includes/head_s.php");
include_once('classes/ChequeDetails.class.php');

$objChequeEntryDetails = new ChequeDetails($m_dbConn);

if(isset($_REQUEST['type']) && $_REQUEST['type'] == 'payment')
{
		if($_SESSION['default_suspense_account'] == 0 || empty($_SESSION['default_suspense_account']))
		{ ?>
        	<script>
					alert("Please first set the suspense ledger on default setting page");
					window.location.href = 'defaults.php';
            </script>
<?php  }
}
?>


<html>
<head>
<link rel="stylesheet" type="text/css" href="css/pagination.css" >
<script type="text/javascript" src="js/validate.js"></script>
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/populateData.js"></script>
<script type="text/javascript">

$(document).ready(

	function(){
		$('input:submit').attr('disabled',true);
		$('input:file').change(
		function(){
				if('<?php echo $_SESSION['is_year_freeze'] ?>' == 0)
				{
					if ($(this).val())
					{
						$('input:submit').removeAttr('disabled'); 
					}
					else 
					{
						$('input:submit').attr('disabled',true);
					}
				}
				else
				{
					$('input:submit').attr('disabled',true);
				}
		});
});
    
</script>

<script language="javascript" type="text/javascript">
function go_error()
{
	setTimeout('hide_error()',10000);	
}
function hide_error()
{
	document.getElementById('error').style.display = 'none';	
}
			
function chgAction( action_name )
{
    if( action_name=="aaa" ) {
    		document.payment_n_receipt_form.action="";
	    }
    else{
        document.payment_n_receipt_form.action="";
    }    
}
</script>
</head>




<body onLoad="go_error();">


<form name="payment_n_receipt_form" action="process/import_paymetns_receipts.process.php" method="post" enctype="multipart/form-data" >

<center>
<br>
<div class="panel panel-info" id="panel" style="display:none">
        <?php if($_REQUEST['type']=='payment'){?>
        <div class="panel-heading" id="pageheader">Import Payment Register</div>
        <?php }else{ ?> 
        <div class="panel-heading" id="pageheader">Import General Receipt Register</div>
        <?php } ?>
<div id="right_menu">
<table>

<?php
if(isset($_POST["ShowData"]))
{
	?>
    <tr height="30"><td colspan=5 style="text-align:center"><font color="red" style="size:11px;"><b id="error"><?php echo $_POST["ShowData"]; ?></b></font></td></tr>
<?php } ?> 
<strong><div id="show" style="text-align:center; width:100%; color:#FF0000"><?php //echo $show_op; ?></div></strong>
<!--<tr height="50" align="center"><td>&nbsp;</td><th colspan="3" align="center"><table align="center"><tr height="25"><th bgcolor="#CCCCCC" width="180">For Society Admin Login</th></tr></table></th></tr>-->
<BR/>
<BR/>

</tr>        
      <tr>
      <td>&nbsp;&nbsp;&nbsp;</td>
      </tr>
<tr align="left">
        	<td valign="middle"></td>
			<td>Browse File To Import</td>
            <td>&nbsp; : &nbsp;</td>
			<td id="browse"><input type="file" name="upload_files[]" id="file" multiple /></td>
            
</tr>   

<tr><td colspan="4">&nbsp;</td></tr>
<tr height="50" align="center">
 <td colspan="4" align="center">
 	<!--<input type="hidden" name="flag" value="4">-->
     <input type="submit" name="Import" id="Import" value="Import" disabled on class="btn btn-primary"  accept="application/msexcel"/></td>

 <!--<td colspan="4" align="center"><input type="submit" name="Import" value="Import"  disabled /></td>-->
</tr>
<input type="hidden" name="type" value="<?php echo $_REQUEST['type'];?>">
<input type="hidden" name="sid" value="<?php echo $_SESSION['society_id'];?>">
</table>

<?php if($_REQUEST['type']=='payment'){?>
        <div style="color:#FF0000">* File Name Should be "Payment.csv"</div><br/>
        <!--<span>Payment Sample File :--> <a href="samplefile/Payment.csv" class="btn btn-primary" style="width:200px; height:30px; font-family:'Times New Roman', Times, serif; font-style:normal; color: #fff; background-color: #337ab7; border-color: #2e6da4;" download> Download Sample </a> <br/><br/>
<?php }else{ ?> 
        <div style="color:#FF0000">* File Name Should be "Receipt.csv"</div><br>
        <!--<span>Receipt Sample File : --><a href="samplefile/Receipt.csv" class="btn btn-primary" style="width:200px; height:30px; font-family:'Times New Roman', Times, serif; font-style:normal; color: #fff; background-color: #337ab7; border-color: #2e6da4;" download> Download Sample </a> <br/><br/>
    <?php } ?>
</center>
</form>
</div>
	<center>
		<table>
			<tr>
				<td>
					<?php
						$voucherType = ($_REQUEST['type'] == 'payment')?VOUCHER_PAYMENT:VOUCHER_RECEIPT;
						
						$data=$objChequeEntryDetails->importBatchPagination($voucherType);
						//print_r($data);?>
						<table id="example" class="display" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th style="text-align:center;">Delete</th>
                        <th style="text-align:center;">Batch Name</th>
                        <th style="text-align:center;">Total Records</th>
                        <th style="text-align:center;">Imported At</th>
                        <th style="text-align:center;">Imported By</th>
                    </tr>
                </thead>
            <tbody>
                <?php	
            	foreach($data as $k => $v)
           		 {
					//echo  $data[$k]['BatchName'];
					?>
					<tr align="center">
					<td><a href="#" onclick=getdeletedata('<?php echo $data[$k]['id']?>');><img src="images/del.gif" width="20"/></a></td>	                
					<td valign="middle" align="center"><?php if($data[$k]['filename'] <> '')
					{?>
                    <a href="<?php echo $data[$k]['filename']?>">
					<?php 
					}?>
					<?php echo $data[$k]['BatchName']?></a></td>
                    <td><?php echo $data[$k]['TotalRecords'];?></td>
                    <td><?php echo $data[$k]['EnteredBy'];?></td>
                    <td><?php echo $data[$k]['Timestamp'];?></td>
                			
				<?php }?>
            </tbody>
        </table>

				
				</td>
			</tr>
		</table>
	</center>
	<input type="hidden" name="voucherType" id="voucherType" value="<?=$voucherType?>">
	</div>
	<script>
		function deleteImportBatch(element_id) {

			try {

				const RECEIPT = 3;
				const PAYMENT = 2;
				let data = element_id.split('-');
				let batch_id = data[1];
				let voucherType = $("#voucherType").val();

				if (batch_id == '' || batch_id == 0) {

					alert('Oops!! Sorry something went wrong!!');
					return false;
				}
				
				if(voucherType == 0 || voucherType == '' || (voucherType != RECEIPT && voucherType != PAYMENT)){

					alert('Sorry something went wrong. Please refresh page or again visit this page');
					return false;
				}

				let userConfirmation = confirm('Are you sure!! You wants to delete.');

				if (userConfirmation) {

					$.ajax({
						url: 'ajax/ajaxBankStatement.php',
						type: "POST",
						data: {
							'method': 'importBatchDelete',
							'batch_id': batch_id, 'voucherType':voucherType
						},
						success: function(response) {

							console.log('response : ', response);
							var a = response.trim();
							var arr1 = new Array();
							var arr2 = new Array();
							arr1 = a.split("@@@");
							arr2 = JSON.parse("[" + arr1[1] + "]");

							console.log('arr2', arr2);
							if (arr2[0].status == 'success') {

								alert(arr2[0].msg);
								window.location.reload();
							} else {
								throw arr2[0].msg;
							}
						}
					});
				}
			} catch (error) {
				alert(error);
			}
		}
	</script>
	<?php include_once "includes/foot.php"; ?>
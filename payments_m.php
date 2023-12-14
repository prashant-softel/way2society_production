<?php include_once "ses_set_s.php"; ?>
<?php include_once("includes/head_s.php");
// include_once("RightPanel.php");    
include_once("classes/home_s.class.php");
include_once("classes/dbconst.class.php");
include_once("datatools.php");
$obj_Admin=new CAdminPanel($m_dbConn);
$get_paymentDetails=$obj_Admin->GetPaymentSummary();
?>

<div id="page-wrapper" style="margin-top:6%;margin-left:3.5%; border:none;width:70%">
<div class="row">
    <div class="col-lg-12">
		<div class="panel panel-default">
        <div class="panel-heading" style="font-size:20px">
            Payments Made To Society So Far...
        </div>
        <div class="panel-body">
	<table id="example" class="display" cellspacing="0">
		<thead>
			<tr>
				<th >Cheque Number</th>
				<th>Date</th>
				<th>Bank</th>
				<th>Amount (Rs.)</th>
            </tr>
		</thead>
		<tbody>
			
				<?php
				if($get_paymentDetails <> "")
				{
					foreach($get_paymentDetails as $key=>$val)
					{
						?>
                        <tr>
                        <td ><?php echo $get_paymentDetails[$key]['ChequeNumber'];?></td>
                        <td ><?php echo getDisplayFormatDate($get_paymentDetails[$key]['VoucherDate']);?></td>
                        <td ><?php echo $get_paymentDetails[$key]['PayerBank'];?></td>
                        <td ><?php echo number_format($get_paymentDetails[$key]['Amount'],2);?></td>
                        </tr>
                <?php }
				}
				else
				{
					echo 'no data';	
				}
				?>
			
		</tbody>
	</table>
</div>
<?php include_once "includes/foot.php"; ?>
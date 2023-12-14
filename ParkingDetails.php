<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Vehicle Parking Report</title>
</head>

<?php if(!isset($_SESSION)){ session_start(); } 
  include_once("includes/head_s.php");
include_once("classes/home_s.class.php");
include_once("classes/dbconst.class.php");
include_once("classes/parking.class.php");
$objParking = new  Parking($m_dbConn);
?>
<html>
<head>
	<script type="text/javascript" src="js/jsDatatableAdvanceFunction.js"></script>
</head>

<body>
<br/>
     <div id="middle">
<br>
<?php
    if($_SESSION['role'] == ROLE_MEMBER || $_SESSION['role'] == ROLE_ADMIN_MEMBER)
    {
        ?>
        <div class="panel panel-info" style="margin-top:6%;margin-left:3.5%; border:none;width:70%">
   <?php
    }
    else
    {
        ?> <div class="panel panel-info" id="panel" style="display:none;"> 
        <!-- <div class="panel panel-info" id="panel" style="display:none;margin-top:10px;margin-left:3.5%;width:75%">-->
        <?php
    }
?>
        <div class="panel-heading" id="pageheader">Vehicle Parking Report</div>

   <center>
<table align="center" border="0" width="100%">
<tr>
	<td valign="top" align="center"><font color="red"><?php if(isset($_GET['del'])){echo "<b id=error_del>Record deleted Successfully</b>";}else{echo '<b id=error_del></b>';} ?></font></td>
</tr>
<?php if(date('d-m-Y') <= '31-01-2020' && $_SESSION['society_id'] == 288)
{ ?>

<form action="process/parking_renew_registration_export.process.php" method="post">
<br>
<tr><td valign="top" align="center"><button type="submit" class="btn btn-primary" >Export Renew Registration Report</button></td></tr>
</form>
<?php }?>

<tr>
<td>
<?php
echo "<br>";
echo $str1 = $objParking->MemberParkingListings();
?>
</td>
</tr>
</table>
</center>
        
      </div>
</div>

</div>
</body>
</html>
<!--<script>
$(document).ready(function() {
	
$('#example').dataTable(
 {
	"bDestroy": true
}).fnDestroy();
datatableRowGrouping(9, 9);
} );
</script>-->
<?php include_once "includes/foot.php"; ?>
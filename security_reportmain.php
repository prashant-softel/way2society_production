<?php include_once "ses_set_as.php"; ?>
<?php
include_once("includes/head_s.php");
include_once("header.php");
include_once("classes/dbconst.class.php");
include_once("classes/home_s.class.php");
$dbConnRoot =new dbop(true);
$dbConn = new dbop();
$obj_home = new CAdminPanel($dbConn,$dbConnRoot);
$result = $obj_home->GetSecurityDB($_SESSION['society_id']);

if(isset($_GET['ssid'])){if($_GET['ssid']<>$_SESSION['society_id']){?><script>window.location.href = "logout.php";</script><?php }}
$today_date = date('Y-m-d');
$last_month = date("Y-m-d", strtotime("-1 month"));

?>
 
<html>
<head>
	<script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
	<script type="text/javascript" src="js/ajax_new.js"></script>
    <script language="javascript" type="application/javascript">
	
	function go_error()
    {
        setTimeout('hide_error()',3000);	
    }
	
    function hide_error()
    {
		document.getElementById('error').innerHTML = '';
        document.getElementById('error').style.display = 'none';	
    }
	
	</script>
    
    <style>
		#block_head{
			background:#337ab7;
			font-family:Verdana, Geneva, sans-serif;
			color:#FFFFFF;
			font-weight:bold;
			font-size:16px;
			padding:5px;
			width:100%;
			text-align:center;
		}
		#block_data{
			
			font-family:Verdana, Geneva, sans-serif;
			color:#000000;
			font-size:14px;
			text-align:center;
			width:100%;
			padding-top:2px;
			padding-bottom:2px;
			border:none;		
		}
		#data_link{
			font-family:Verdana, Geneva, sans-serif;
			color:#337ab7;
			text-align:center;
			width:100%;		
		}
	</style>
   
<title>Report</title>
</head>

<?php if(isset($_REQUEST['ShowData']) || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])){ ?>
<body onLoad="go_error();">
<?php }else{ ?>
<body>
<?php } ?>

<br>
<div id="middle">
<div class="panel panel-info" id="panel" style="display:none">
    <div class="panel-heading" id="pageheader">Security Manager Module</div>

<?php if(!isset($_REQUEST['ws'])){ $val ='';?>
<?php }else{ $val = 'onSubmit="return val();"';
?>
<br>
<center>
<a href="wing.php?imp&ssid=<?php echo $_REQUEST['ssid'];?>&s&idd=<?php echo time();?>" style="color:#00F; text-decoration:none;"><b>Back</b></a>
<?php } ?>
<br><br>

<table style="width:100%;">
<tr>
	<td style="width:50%;"><div id="block_head">Report</div></td>
	<td style="width:50%;"><div id="block_head">Security Schedule</div></td>
</tr>
<tr>
	<td>
	<button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('ReportDashboard.php?startdate=<?php echo $last_month;?>&enddate=<?php echo $today_date;?>', '_blank')">Report Dashboard</button>
	<button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('Sm_Report.php?Visitor', '_blank')"> Visitor Report</button>
    <button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('Sm_Report.php?Staff', '_blank')"> Staff Report</button>
	<button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('schedule_list_report.php?type=0', '_blank')">Security Schedule Report</button>
   </td>  
    <td>
     
    
    	<table style="width:100%;">			
			<tr>
				<td>
				 <button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('checkpost_master.php', '_blank')">Check Post Master</button>
				 <button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('round_master.php', '_blank')">Round Master</button>
				 <button type="button"  style="border:none" class="btn btn-outline btn-primary btn-lg btn-block" onClick="window.open('shedule_list.php', '_blank')">Schedule Master</button>
     			</td>
			</tr>
       </table>
      
	</td>
  </tr>
</table>
<br>
</center>
<br/>
</div>
</div>
<?php include_once "includes/foot.php"; ?>

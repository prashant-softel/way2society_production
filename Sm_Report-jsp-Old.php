<?php include_once "ses_set_as.php"; ?>
<?php
$title="W2S - Staff Reports";
	include_once("includes/head_s.php");
//}
include_once("header.php");
include_once("classes/dbconst.class.php");
include_once("classes/home_s.class.php");
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
<title>Untitled Document</title>
</head>

<body>
<br>
<div id="middle">
<div class="panel panel-info" id="panel" style="display:none">
<?php if($_REQUEST['report'] == 'visitor')
	{?>
    <div class="panel-heading" id="pageheader">Visitor Reports</div>
    <iframe src="http://way2society.com:8080/NewSecurityManagerWeb1/visitorReport.jsp" height="600" width="100%"></iframe>
    
    <?php 
	}
	else
	{?>
	 <div class="panel-heading" id="pageheader">Staff Reports</div>	
     
     <iframe src="http//way2society.com:8080/NewSecurityManagerWeb1/staffReport.jsp" height="600" width="100%"></iframe>
	<?php }?>
    
    
    </div>
    </div>
</body>
</html>

<?php include_once "includes/foot.php"; ?>
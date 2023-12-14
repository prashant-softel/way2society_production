<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
<title>W2S - Bill Validation</title>
</head>


<?php
include_once "classes/include/check_session.php";
include_once "classes/billDetailsValidation.class.php";

$obj_billValidate = new billValidation($m_dbConn);
$society_Name = $obj_billValidate->getSocietyName($_SESSION['society_id']);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Way2Society-Bill validation Report</title>
<script language="javascript">
	var currentdate = new Date();
	var hours = currentdate.getHours();
	hours = hours % 12;
  	hours = hours ? hours : 12; 
	var datetime = currentdate.getDate() + "/"+(currentdate.getMonth() + 1) + "/" + currentdate.getFullYear() + " " + hours + ":" 					
					+ ((currentdate.getMinutes() < 10)? ("0" + currentdate.getMinutes()): (currentdate.getMinutes())) + ':' + 
					((currentdate.getSeconds() < 10) ? ("0" + currentdate.getSeconds()) : (currentdate.getSeconds()));						    		
</script>
 </head>
 <body>
<center>
<div style="color:#0033CC;">Way2Society-Bill validation Report - <?php echo $society_Name; ?> [<script> document.write(datetime); </script>]</div>
</center>

<?php
if(isset($_REQUEST['developer']))
{ 
	$units = $obj_billValidate->ValidateBillDetailsTable("developer");
}
else if(isset($_REQUEST['report']))
{ 
	$units = $obj_billValidate->ValidateBillDetailsTable("report");
}
else
{
	$units = $obj_billValidate->ValidateBillDetailsTable("report");
}
?>
</body>
</html>
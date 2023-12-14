
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
<title>W2S - Society Group Events</title>
</head>


<?php include_once "ses_set_as.php"; ?>
<?php
if(isset($_SESSION['admin']))
{
	include_once("includes/header.php");
}
else
{
	include_once("includes/head_s.php");
}

include_once("classes/events.class.php");
$obj_events = new events($m_dbConn,$m_dbConnRoot);
?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/jsevents20190504.js"></script>
    <script language="javascript" type="application/javascript">
	function go_error()
    {
        setTimeout('hide_error()',10000);	
		
    }
    function hide_error()
    {
        document.getElementById('error_del').style.display = 'none';	
    }	
	</script>
    
    <!--<link rel="stylesheet" href="css/ui.datepicker.css" type="text/css" media="screen" />
	<script type="text/javascript" src="javascript/jquery-1.2.6.pack.js"></script>
    <script type="text/javascript" src="javascript/jquery.clockpick.1.2.4.js"></script>
    <script type="text/javascript" src="javascript/ui.core.js"></script>
    <script type="text/javascript" src="javascript/ui.datepicker.js"></script>-->
    <script language="JavaScript" type="text/javascript" src="js/validate.js"></script> 
    <script type="text/javascript">
        $(function()
        {
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker({ 
            dateFormat: "yy-mm-dd", 
            showOn: "both", 
            buttonImage: "images/calendar.gif", 
            buttonImageOnly: true 
        })});
            
    </script>
</head>

<?php if(isset($_REQUEST['del']) || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])  || isset($_REQUEST['add'])){ ?>
<body onLoad="go_error();">
<?php }else{ ?>
<body>
<?php } ?>

<div id="middle">
<center>
<br>
<div class="panel panel-info" id="panel" style="display:none">
<div class="panel-heading" id="pageheader">Groups Events</div>
<br>
<center>
<?php if(!isset($_SESSION['sadmin'])){?>
<br>
<center>
<a href="events.php?ev" style="color:#00F; text-decoration:none;"><b><u>Add Event</u></b></a>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href="events_view_as_self.php?ev" style="color:#00F; text-decoration:none;"><b><u>My Society Event</u></b></a>
</center>
<br>
<?php } ?>

<?php if(isset($_SESSION['sadmin'])){?>
<br>
<center>
<a href="events_view_as_self.php?ev" style="color:#00F; text-decoration:none;"><b><u>Individual society event</u></b></a>
</center>
<br>
<form method="get">
<table align="center" border="0">
<tr>
	<td>Society</td>
    <td>&nbsp; : &nbsp;</td>
    <td>
    <select name="society_id" id="society_id" style="width:250px;" onChange="get_wing(this.value);">
	<?php echo $combo_society = $obj_events->combobox07("select society_id,concat_ws(' - ',society_name,landmark) from society where status='Y' order by society_id desc",$_REQUEST['society_id']); ?>
    </select>
    </td>
</tr>

<tr><td colspan="3">&nbsp;</td></tr>

<tr><td colspan="3" align="center"><input type="submit" name="search" id="insert" value="Search"></td></tr>

</table>
<?php }?>
<input type="hidden" name="ev" value="ev">
</form>

<table align="center" border="0">
<tr>
	<td valign="top" align="center"><font color="red"><?php if(isset($_GET['del'])){echo "<b id=error_del>Record deleted Successfully</b>";}else if(isset($_GET['add'])){echo "<b id=error_del>Record added Successfully</b>";}else{echo '<b id=error_del></b>';} ?></font></td>
</tr>

<tr>
<td>
<?php
echo "<br>";
$str1 = $obj_events->pgnation();
echo "<br>";
/*echo $str = $obj_events->display1($str1);
echo "<br>";
$str1 = $obj_events->pgnation();
*/
echo "<br>";
?>
</td>
</tr>
</table>


</center>
</div>
</center>
</div>
</body>
<?php include_once "includes/foot.php"; ?>
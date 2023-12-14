
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
<title>W2S - Society Events</title>
</head>




<?php
	include_once("includes/head_s.php");
	include_once ("classes/dbconst.class.php");

include_once("classes/events_self.class.php");
$obj_events_self = new events_self($m_dbConn,$m_dbConnRoot);
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
<div class="panel-heading" id="pageheader">My Society Events</div>
<br>
<center>

<?php if(!($_SESSION['role']==ROLE_MEMBER && $_SESSION['role']==ROLE_ADMIN_MEMBER)){?>
<br>
<center>
<a href="events.php?ev" style="color:#00F; text-decoration:none;"><b><u>Add Event</u></b></a>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<!--<a href="events_view_as.php?ev" style="color:#00F; text-decoration:none;"><b><u>Group Events</u></b></a>-->
</center>
<br>
<?php } ?>

<?php if(isset($_SESSION['role']) && $_SESSION['role']==ROLE_SUPER_ADMIN){?>
<br>
<center>
<!--<a href="events_view_as.php?ev" style="color:#00F; text-decoration:none;"><b><u>Group Event</u></b></a>-->
</center>
<br>

<form method="get">
<table align="center" border="0">
<tr>
	<td>Society</td>
    <td>&nbsp; : &nbsp;</td>
    <td>
    <select name="society_id" id="society_id" style="width:250px;" onChange="get_wing(this.value);">
	<?php echo $combo_society = $obj_events_self->combobox07("select society_id,concat_ws(' - ',society_name,landmark) from society where status='Y' order by society_id desc",$_REQUEST['society_id']); ?>
    </select>
    </td>
</tr>

<tr><td colspan="3">&nbsp;</td></tr>

<tr><td colspan="3" align="center"><input type="submit" name="search" value="Search" id="insert"></td></tr>

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

$str1 = $obj_events_self->pgnation($_REQUEST['ev']);
/*
for($i=0;$i<(sizeof($str1)/3);$i++)
			{
				?>
               
                <div style="width:100%;" style="width:100%" >
               <?php 
				for($data=0;$data <3;$data++)
				{
				
                ?>
                <div style="width:30%; float:left;" class="panel panel-primary">
                <div>
                 <dd style="float:left;"><?php echo $str1[$data]['events_date'];?></dd>
				 <dd style="float:left;"><?php echo $str1[$data]['event_time'];?></dd>
                </div>
                </div>
                
                <div class="main_block" >
                <div><?php echo $str1[$data]['events'];?></div>
                </div>
                
          <?php      
			}
			?>
            </div>
            <?php
		}*/
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

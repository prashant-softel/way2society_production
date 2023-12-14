<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><head>
    <title>W2S - Security Managment Report</title>
    <script type="text/javascript" src="js/jscheckpost_master.js"></script>
    <script language="javascript" type="application/javascript">
	function go_error()
    {
		$(document).ready(function()
		{
			$("#error").fadeIn("slow");
		});
        setTimeout('hide_error()',8000);	
    }
    function hide_error()
    {
		$(document).ready(function()
		{
			$("#error").fadeOut("slow");
		});
    }
	</script>
    
    
</head>

<?php 
//******Including the required File for SM Report 
include_once("includes/head_s.php");  
include_once("classes/include/dbop.class.php");
include_once("classes/checkpost_master.class.php");
//*****Making different object to connect different databases
$dbConn = new dbop();
$dbConnRoot = new dbop(true);
$smConn = new dbop(false,false,true,false);
$smConnRoot = new dbop(false,false,false,true);

$smreport = new SM_Report($dbConn,$dbConnRoot,$smConn,$smConnRoot);
$getcheckpostreport = $smreport -> getcheckpost_report();
?>
<?php
$star = "<font color='#FF0000'>*</font>";
if(isset($_REQUEST['msg']))
{
	$msg = "Sorry !!! You can't delete it. ( Dependency )";
}
else if(isset($_REQUEST['msg1']))
{
	$msg = "Deleted Successfully.";
}
else{}
?>
<div style="text-align:center" class="panel panel-info" style="border:none;width:100%">

<div class="panel-heading text-center" style="font-size:20px">
    Checkpost Master
</div>
<center>
<form name="schedulemaster" id="schedulemaster" method="post"  action="process/checkpost_master.process.php" enctype="multipart/form-data" onSubmit="return val();">
<input type="hidden" name="id" id="id" value="<?php echo $_REQUEST['id']; ?>">


<table align='center'>

<?php
if(isset($msg))
{
	if(isset($_POST['ShowData']))
	{
?>
		<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $_POST['ShowData']; ?></b></font></td></tr>
<?php
	}
	else
	{
	?>
		<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $msg; ?></b></font></td></tr>
	<?php
	}
}
else
{
?>
		<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $_POST['ShowData']; ?></b></font></td></tr>
<?php
}
?>

  <tr>
      <td valign="middle"><?php echo $star;?></td>
      <td> Name </td>
      <td>&nbsp;&nbsp; : &nbsp;&nbsp;</td>
      <td> 
      <input type="text" name="name1" id="name1">
      </td>
  </tr>
  <tr>
      <td valign="middle"><?php //echo $star;?></td>
      <td> Description </td>
      <td>&nbsp;&nbsp; : &nbsp;&nbsp;</td>
      <td> 
      <input type="text" name="desc" id="desc">
      </td>
  </tr>

  <tr>
    <td colspan="4" style="text-align:center;"></br></br>
        <input type="submit" name="insert" id="insert" value="Insert" class="btn btn-primary" style="color:#FFF; width:100px;background-color:#337ab7;"></td>
    </td>
  </tr>
</center>
</table>
</form>
<table id="example" class="display" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th style="text-align:center;">Checkpost Name</th>
                        <th style="text-align:center;">Description</th>
                        <th style="text-align:center;">qrcode</th>
                        <th style="text-align:center;">Generate qrcode</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                </thead>
            <tbody>
            <?php	
            	foreach($getcheckpostreport as $k => $v)
           		 {
					?>
					<tr align="center">	                
             		<td><?php echo $getcheckpostreport[$k]['checkpost_name'];?></td>
             		<td><?php echo $getcheckpostreport[$k]['desc'];?></td>
                    <td><?php echo $getcheckpostreport[$k]['qrcode'];?></td>               
                    <td><input type="button" id="genqrcode" name="genqrcode" value="Generate qrcode" class="btn btn-primary" style="padding:3px 6px;"  onclick="window.location.href='checkpost_qrcode.php?id=<?php echo $getcheckpostreport[$k]['id'];?>'"></td>
                    <td  valign="middle" align="center"> <a href="checkpost_master.php?id=<?php echo $getcheckpostreport[$k]['id'];?>&edit" style="color:#00F"><img src="images/edit.gif" width="16" /></a></td>
                    <td  valign="middle" align="center"> <a href="checkpost_master.php?deleteid=<?php echo $getcheckpostreport[$k]['id'];?>&del" style="color:#00F"><img src="images/del.gif" width="16"  /></a></td> 		   
                			
				<?php }?>
           </tbody>
        </table>
        </div>
</body>

<?php 
if(isset($_REQUEST['id']) && $_REQUEST['id'] <> '')
{ ?>
    <script>
        get_checkpost_master_edt(<?php echo $_REQUEST['id']; ?>)
    </script>
<?php }
if(isset($_REQUEST['deleteid']) && $_REQUEST['deleteid'] <> '')
{ ?>
    <script>
        get_checkpost_master_del(<?php echo $_REQUEST['deleteid']; ?>)
    </script>
<?php } ?>
<?php include_once "includes/foot.php"; ?>
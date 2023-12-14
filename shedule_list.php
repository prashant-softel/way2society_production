<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><head>
    <title>W2S - Security Managment Report</title>
    <script type="text/javascript" src="js/schedule_list.js"></script>
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
    function go1()
    {
        document.getElementById("insert").focus();
    }	

	
</script>
</head>

<?php 
//******Including the required File for SM Report 
include_once("includes/head_s.php");  
include_once("classes/include/dbop.class.php");
include_once("classes/schedulelist.class.php");
//*****Making different object to connect different databases
$dbConn = new dbop();
$dbConnRoot = new dbop(true);
$smConn = new dbop(false,false,true,false);
$smConnRoot = new dbop(false,false,false,true);

$smreport = new SM_Report($dbConn,$dbConnRoot,$smConn,$smConnRoot);
$getschedule_list_report = $smreport ->getschedule_list_report();
//var_dump($getschedule_list_report);
?>
<?php if(isset($_REQUEST['ShowData']) || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])){ ?>
<body onLoad="go_error();go1();">
<?php }else{ ?>
<body onLoad="go1();">
<?php } ?>


<div style="text-align:center" class="panel panel-info" style="margin-top:3%;margin-left:3.5%; border:none;width:100%">

<div class="panel-heading text-center" style="font-size:20px">
    Schedule List
</div>
<center>
<table>
<form name="schedulemaster" id="schedulemaster" method="post"  action="process/schedulelist.process.php" enctype="multipart/form-data" onSubmit="return val()";>
<?php
	if(isset($msg))
	{
		if(isset($_POST["ShowData"]))
		{
		?>
			<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $_POST["ShowData"]; ?></b></font></td></tr>
		<?php
		}
		else
		{
		?>
        <tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $msg; ?></b></font></td></tr>	   
        <?php		
		}
	}
	else
	{
	?>	
		<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $_POST["ShowData"]; ?></b></font></td></tr>
    <?php
	}
	?>
<input type="hidden" name="id" id="id" value="<?php echo $_REQUEST['id']; ?>">

<?php
    $star = "<font color='#FF0000'>*</font>";
?>


<tr><td><br></td></tr>
  <tr>
      <td valign="middle"><?php echo $star;?></td>
      <td> Schedule Name </td>
      <td>&nbsp;&nbsp; : &nbsp;&nbsp;</td>
      <td> 
      <input type="text" name="schedulename" id="schedulename">
      </td>
  </tr>
  
  <tr>
      <td valign="middle"><?php echo $star;?></td>
      <td> Round Type </td>
      <td>&nbsp;&nbsp; : &nbsp;&nbsp;</td>
      <td> 
      <select name="roundtype" id="roundtype" tabindex="1">
	<?php echo $combo_state=$smreport->combobox("select ID,Name from round_master",'0');?>  
	</select>      
</td>

  </tr>
  <tr>
    <td valign="middle"><?php echo $star;?></td>
    <td> Frequency </td>
    <td>&nbsp;&nbsp; : &nbsp;&nbsp;</td>
    <td> 
    <select name="frequency" id="frequency" tabindex="1">
        <option value="0">Please Select</option>
        <option value="Daily">Daily</option>
        <option value="Weekly">Weekly</option>
        <option value="Monthly">Monthly</option>
        <option value="Annual">Annual</option>
	</select>      
    </td>
  </tr>
  
  <tr>
    <td valign="middle"><?php echo $star;?></td>
    <td> Time </td>
    <td>&nbsp;&nbsp; : &nbsp;&nbsp;</td>
    <td> 
    <select name="time" id="time">
          <option> Please Select </option>
          <?php for($i = 0; $i < sizeof($DEFINETIME); $i++){								
              echo "<option value=".$DEFINETIME[$i]['id'].">" .$DEFINETIME[$i]['time']."</option>";
          }
          ?>
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
                        <th style="text-align:center;">Schedule Name</th>
                        <th style="text-align:center;">Round Type</th>
                        <th style="text-align:center;">Frequency</th>
                        <th style="text-align:center;">Round Time</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                </thead>
            <tbody>
            <?php	
            	foreach($getschedule_list_report as $k => $v)
           		 {
					?>
					<tr align="center">	                
             		<td><?php echo $getschedule_list_report[$k]['schedule_name'];?></td>
             		<td><?php echo $getschedule_list_report[$k]['round_id'];?></td>
                    <td><?php echo $getschedule_list_report[$k]['frequency'];?></td>
                    <td><?php echo $getschedule_list_report[$k]['round_time'];?></td>
                    <td  valign="middle" align="center"> <a href="shedule_list.php?id=<?php echo $getschedule_list_report[$k]['id'];?>&edit" style="color:#00F"><img src="images/edit.gif" width="16" /></a></td>
                    <td  valign="middle" align="center"> <a href="shedule_list.php?deleteid=<?php echo $getschedule_list_report[$k]['id'];?>&del" style="color:#00F"><img src="images/del.gif" width="16"  /></a></td> 		   
                			
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
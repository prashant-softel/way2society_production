<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><head>
    <title>W2S - Schedule List Report</title>
    <script language="javascript" type="application/javascript">
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

if(isset($_REQUEST['type']) && $_REQUEST['type'] == "0")
{
	$getschedule_list_report = $smreport ->schedule_list_report($_REQUEST['type']);
}

else if(isset($_REQUEST['type']) && $_REQUEST['type'] == "1")
{
	$getschedule_list_report = $smreport ->schedule_list_report($_REQUEST['type']);
}
?>
<div style="text-align:center" class="panel panel-info" style="margin-top:3%;margin-left:3.5%; border:none;width:100%">

<div class="panel-heading text-center" style="font-size:20px">
    Schedule List Report
</div>
<br>
<center>
<table style="width:100px;">
    <td>
        <input type="button" name="fetch" id="fetch" value="Report" class="btn btn-primary" style="color:#FFF; width:100px;background-color:#337ab7;" onclick="window.location.href='SecurityRound_report.php'"></td>
    </td>
  </tr>
</table>
</center>
<div class="panel-body">                        
        <div class="table-responsive">
        <ul class="nav nav-tabs" role="tablist">  
            <li <?php echo (isset($_REQUEST['type']) && $_REQUEST['type'] == "0" && $_REQUEST['type'] <>"1" ) ? 'class="active"' : ""; ?>> 
            	<a href="#upcoming" role="tab" data-toggle="tab" onClick="window.location.href='schedule_list_report.php?type=0'">Upcoming</a>
    		</li>           
            
            <li <?php echo (isset($_REQUEST['type']) && $_REQUEST['type'] == "1" && $_REQUEST['type'] <> "0" ) ? 'class="active"' : ""; ?>> 
            	<a href="#completed" role="tab" data-toggle="tab" onClick="window.location.href='schedule_list_report.php?type=1'">Completed</a>
    		</li>        
        </ul>
		<br/>
        <table id="example" class="display" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th style="text-align:center;">Schedule_name</th>
                        <th style="text-align:center;">Time</th>
                        <th style="text-align:center;">Frequency</th>
                        <th style="text-align:center;">No_of_Checkpost</th>
                    </tr>
                </thead>
                <tbody>
                	<?php 
            	foreach($getschedule_list_report as $k => $v)
                {?>
                	<tr>
                        <td><?php echo $getschedule_list_report[$k]['schedule_name'];?></td>
                        <td><?php echo $getschedule_list_report[$k]['rtime'];?></td>
                        <td><?php echo $getschedule_list_report[$k]['frequency'];?></td>
                        <td><?php echo $getschedule_list_report[$k]['no_of_checkpost'];?></td>
                    </tr>
          <?php } ?>
                </tbody>
        </table>
</div>
</div>
</div>
<?php include_once "includes/foot.php"; ?>
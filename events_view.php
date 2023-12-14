
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
<title>W2S - Event List</title>
</head>




<?php //include_once "ses_set_m.php"; ?>
<?php include_once("includes/head_s.php");
include_once "ses_set_s.php";
include_once("classes/home_s.class.php");
include_once("classes/dbconst.class.php");
include_once("classes/events.class.php");
$obj_events = new events($m_dbConn, $m_dbConnRoot);
$events = $obj_events->view_events($_REQUEST['ev']);
?>
<div class="panel panel-info" style="margin-top:6%;margin-left:3.5%; border:none;width:70%">
 
    <div class="panel-heading" style="font-size:20px;text-align:center;">
        List of Events
    </div>
    <br />
     <?php
	if($_SESSION['role'] && $_SESSION['is_year_freeze'] == 0 && ($_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['profile']['events.php'] == 1))
	      {?>
          <center><button type="button" class="btn btn-primary" onClick="window.location.href='events.php'">Add New Event</button></center>
          
         <!-- <a href="addnotice.php" >Add New</a>-->
    <?php }?>
	<div class="panel-body">                        
        <div class="table-responsive">
            <table id="example" class="display" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th style="width:60px;">Issued By</th>
                        <th style="width:100px;">Event Title</th>
                        <th style="width:60px;">Event Date</th>
                         <th style="width:60px;">Event End Date</th>
                        <th style="width:60px;">Event Time</th>
                        <th style="width:80px;">Participation Charges (Rs.)</th>
                      
                        <?php 
	if($_SESSION['role'] && $_SESSION['is_year_freeze'] == 0 && ($_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['profile']['events.php'] == 1))
	      				{?>
                        <th style="width:10px;">Edit</th>
                        <th style="width:10px;">Delete</th>
                        <?php }?>
                    </tr>
                </thead>
                <tbody>
                <?php	
            	foreach($events as $k => $v)
           		 {
                 if(strtotime($events[$k]['end_date']) >= strtotime(date('Y-m-d')))
					{?>
					 <tr >
				 <?php
				 	} 
				 else
					{?>
					 <tr  style=" color:#999999;">
			   <?php }?> 
                
             		<td><?php echo $events[$k]['issued_by'];?></td>
                	<td> <a href="events_view_details.php?id=<?php echo $events[$k]['events_id'];?>&ev"><?php echo $events[$k]['events_title'];?></a></td>
                	<td><?php echo getDisplayFormatDate($events[$k]['events_date']);?></td>
                    <td><?php echo getDisplayFormatDate($events[$k]['end_date']);?></td>
                	<td style="text-align:center"><?php echo $events[$k]['event_time']; ?></td>
                	<td style="text-align:center">
                    <?php if($events[$k]['event_charges'] > 0)
					{?>
					<?php echo $events[$k]['event_charges']; ?>
                    <?php }
					else
					{
					 echo "Free";
					}?>
                    </td>
                    
                 
				 <?php 
				   
	if($_SESSION['role'] && $_SESSION['is_year_freeze'] == 0 && ($_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['profile']['events.php'] == 1))
				   {?>
					   <td  valign="middle" align="center"> <a href="events.php?id=<?php echo $events[$k]['events_id'];?>&ev" style="color:#00F"><img src="images/edit.gif" width="16" /></a></td>
                       <td  valign="middle" align="center"><a href="events.php?deleteid=<?php echo $events[$k]['events_id'];?>&ev" style="color:#00F"><img src="images/del.gif" width="16"  /></a></td> 
				   <?php 
				   }
                 
				 }?>
                 </tbody>
            </table>
        </div>
            
</div>

</div>

<?php include_once "includes/foot.php"; ?>

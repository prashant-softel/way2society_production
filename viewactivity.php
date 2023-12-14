<?php include_once "ses_set_s.php"; ?>
<?php include_once("includes/head_s.php");
include_once("includes/dbop.class.php");
//include_once("classes/home_s.class.php");
//include_once("classes/dbconst.class.php");
include_once("classes/notice.class.php");

$dbConnRoot = new dbop(true);
$obj_notice = new notice($m_dbConn);
//$obj_utility = new utility($m_dbConn, $dbConnRoot);
 
$display_activities=$obj_notice->FetchActivities($_REQUEST['id']);

//$prevID = "";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>

<link rel="stylesheet" type="text/css" href="css/pagination.css" >
<script type="text/javascript" src="js/ajax.js"></script>
<div class="panel panel-info" style="margin-top:6%;margin-left:1%; border:none;width:77%">
 
    <div class="panel-heading" style="font-size:20px;text-align:center;">
         Activity Status 
    </div>
    <br />
    <button type="button" class="btn btn-primary" onclick="window.location.href='notices.php?in=0'">Back to list</button>
    <br />
    <div class="panel-body">                        
        <div class="table-responsive">
            <table id="example" class="display" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th  style="text-align: center;">Sr.No</th>
                        <th style="text-align: center;">Unit No.</th>
                        <th  style="text-align: center;">Member Name</th>
                        <th  style="text-align: center;">Email Time</th>
                        <th  style="text-align: center;">Email Status</th>
                        <th  style="text-align: center;">SMS Time</th>
                        <th  style="text-align: center;">SMS Status</th> 
                        <th  style="text-align: center;">Notification Time</th>
                        <th  style="text-align: center;">Notification Status</th>
                        
                        </tr>
                </thead>
                <tbody>
                <?php
				 for($i=0;$i<sizeof($display_activities); $i++)
				{	
					$srcount++;
					if($display_activities[$i]['SendNoticeEmailDate'] == '0000-00-00 00:00:00')
					{
						$EmailTime= $display_activities[$i]['SendNoticeEmailDate'];
					}
					else
					{
						$EmailTime= date('Y-m-d H:i',strtotime('+5 hour +30 minutes',strtotime($display_activities[$i]['SendNoticeEmailDate'])));
					}
					if($display_activities[$i]['SendNoticeSMSDate'] == '0000-00-00 00:00:00')
					{
						$SMSTime=$display_activities[$i]['SendNoticeSMSDate'];
					}
					else
					{
						$SMSTime= date('Y-m-d H:i',strtotime('+5 hour +30 minutes',strtotime($display_activities[$i]['SendNoticeSMSDate'])));
					}
					if($display_activities[$i]['SendNotificationDate'] == '0000-00-00 00:00:00')
					{
						$NotificationTime= $display_activities[$i]['SendNotificationDate'];
					}
					else
					{
						$NotificationTime= date('Y-m-d H:i',strtotime('+5 hour +30 minutes',strtotime($display_activities[$i]['SendNotificationDate'])));
					}
					
					
					?>
				
               		<tr>
                	<td align="center"><?php echo $srcount ?></td>
               		<td align="center"><?php echo $display_activities[$i]['unit_no'];?></td>
                	<td align="center"><?php echo $display_activities[$i]['owner_name'];?></td></td> 
                 	<td align="center"><?php echo $EmailTime;?></td>
                 	<td align="center">
					<?php if($display_activities[$i]['SendStatus'] == null)
				 	{
					 	echo "-";
				 	}
				 	else
				 	{
				 		echo $display_activities[$i]['SendStatus'];
				 	}?>
                    </td>
             		<td align="center"><?php echo $SMSTime;?></td>
                	<td align="center">
					<?php 
					if($display_activities[$i]['sms_status'] == null)
					{
						echo "Contact no. not available";
					}
					else
					{
						echo $display_activities[$i]['sms_status'];
					}
					?>
                	</td>
                	<td align="center"><?php echo $NotificationTime;?></td>
               		<td align="center">
					<?php if($display_activities[$i]['Mobile_Notification'] == null)
					{
						echo "-";
					}
					else
					{
						echo $display_activities[$i]['Mobile_Notification'];
					}
					?>
                    </td>
                </tr>
                <?php }?>
                </tbody>
            </table>
        </div>
     
</div>

</div>

<?php include_once "includes/foot.php"; ?>
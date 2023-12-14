<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Polls List</title>
</head>

<?php include_once "ses_set_s.php"; ?>
<?php include_once("includes/head_s.php");
// include_once("RightPanel.php");    
include_once("classes/home_s.class.php");
include_once("classes/dbconst.class.php");
include_once("classes/create_poll.class.php");
include_once("classes/utility.class.php");
$obj_create_poll = new create_poll($m_dbConnRoot,$m_dbConn);
$obj_utility=new utility($m_dbConn);
$requests = $obj_create_poll->getRecords($_REQUEST['cm']);
//print_r($requests);
?>
<style>
    .link{display:inline}
    .link {float: left}
	.disabled {
   pointer-events: none;
   cursor: default;
}
	
</style>
<!--<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="js/ajax_new.js"></script>
<script type="text/javascript" src="js/jsServiceRequest.js"></script>
<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
-->

<div class="panel panel-info" style="margin-top:6%;margin-left:3.5%; border:none;width:70%">

    <div class="panel-heading" style="font-size:20px">
    <center>  Polls List  </center>
    </div>
  
    <br />
    <center>
    <?php if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN ||$_SESSION['role']==ROLE_ACCOUNTANT || $_SESSION['role']==ROLE_ADMIN_MEMBER || $_SESSION['role']==ROLE_SUPER_ADMIN))
	{
							 
if(($_SESSION['is_year_freeze'] == 0 && $_SESSION['profile']['create_poll.php'] == 1) || $_SESSION['role']==ROLE_SUPER_ADMIN)
{?>
   <center><button type="button" class="btn btn-primary" onClick="window.location.href='create_poll.php'">Add New Poll</button></center>
<?php }?>
</center>   
    <!--<span class="link"><a href="addservicerequest.php">Create New Service Request</a></span> -->
    <br />
    <div class="panel-body">                        
        <div class="table-responsive">
            <table id="example" class="display" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Issued By</th>
                        <th>Question</th>
                        <th>Start Date</th>
                        <th>End Date</th>                        
                         <th>View</th>
                         <th>Edit</th>
                        <th>Delete</th>                                                                      
                    </tr>
                </thead>
                <tbody>
                	<?php 
						$prevRequestNo = "";
						for($i = 0; $i < sizeof($requests); $i++)
						{							
							if($prevRequestNo != $requests[$i]['poll_id'])
							{
								//$status = $obj_create_poll->getUpdatedStatus($requests[$i]['poll_id']);
								$prevRequestNo = $requests[$i]['poll_id'];
					?>
                    <tr>
                        <td><?php echo $requests[$i]['name'];?></td>
                         <td><a href="view_polldetails.php?rq=<?php echo $obj_utility->encryptData($requests[$i]['poll_id']);?>"><?php echo $requests[$i]['question'];?></a></td>
                        <td><?php echo getDisplayFormatDate($requests[$i]['start_date']);?></td>
                        <td>
                        <?php 
						$expdate = $obj_utility->getDateDiff(getDBFormatDate($requests[$i]["end_date"]), date("Y-m-d"));
						if($expdate>0)
						{?>
                        <?php echo getDisplayFormatDate($requests[$i]['end_date']);?></td>
						<?php }
						else {?>
								<span style="color:#F00;"><?php echo getDisplayFormatDate($requests[$i]['end_date']);?></span></td>		
								<?php }?>
						
                         <td><a href="view_polldetails.php?rq=<?php echo $obj_utility->encryptData($requests[$i]['poll_id']);?>"><img src="images/view.jpg"  width="20"/></a></td>
                      <!--  <td align="center">  <a href="addnotice.php?id=<?php //echo $display_notices[$key]['id'];?>"><img src="images/view.jpg"  width="20"/></a> </td>-->
                       <td> 
                         <?php 
						 $date = $obj_utility->getDateDiff(getDBFormatDate($requests[$i]["start_date"]), date("Y-m-d"));
						 // $expdate = $obj_utility->getDateDiff(getDBFormatDate($requests[$i]["end_date"]), date("Y-m-d"));
						 
							if($date > 0)
							{?>
                            <a href="create_poll.php?edit=<?php echo $requests[$i]['poll_id'];?>"><img src="images/edit.gif" /></a>
							<?php }
                          //  else if( $expdate> 0)
						  else {?>
                           <!-- <span>Active</span>-->
                                <a class="disabled" href="create_poll.php?edit=<?php echo $requests[$i]['poll_id'];?>"><img  style="display:none;"src="images/edit.gif" /></a>
                            <?php }
                                  //  else{   ?> 
                                  <!--<span> Expire</span>-->
                                    <?php //}?>       	
                               <!-- <a  class="disabled" href="create_poll.php?edit=<?php //echo $requests[$i]['poll_id'];?>"><img src="images/edit.gif" /></a>-->
                				<!--<a id="edit" href="javascript:void(0);" onclick="getNotice(this.id, <?php //echo $display_notices[$key]['id'];?>);"><img src="images/edit.gif" /></a> -->
                			</td>
                             <td>
                             <?php
							 if($date> 0)
							 {?>                            	
                                <a href="create_poll.php?deleteid=<?php echo $requests[$i]['poll_id'];?>"><img src="images/del.gif" /></a>
                                <?php }
								else
								{?>
                                      <a href="create_poll.php?deleteid=<?php echo $requests[$i]['poll_id'];?>"><img  style="display:none" src="images/del.gif" /></a> 
                                      <?php } ?>         				
                			</td>
                       
                    </tr>
                    <?php
							}
						}
					?>
                </tbody>
                <!--<tbody>
                	<?php if($_REQUEST['cm'] == 1 || $_REQUEST['cm'] == 0)
						  {?>
                    <tr>
                        <td>1</td>
                        <td>Newspaper</td>
                        <td>News paper not delivered for 6 days starting 30st April</td>
                        <td>6th May 2015</td>
                        <td>Active</td>
                        <td>6th May 2015</td>
                        <td>5</td>
                        <td>View</td>
                    </tr>
                    <?php
						  }
						  if($_REQUEST['cm'] == 2 || $_REQUEST['cm'] == 0)
						  {
					?>
                    <tr>
                        <td>2</td>
                        <td>Swimming Pool</td>
                        <td>Swimming pool water looks too dirty</td>
                        <td>6th May 2015</td>
                        <td>Active</td>
                        <td>6th May 2015</td>
                        <td>2</td>
                        <td>View</td>
                    </tr>                    
                    <?php
						  }
					?>
                </tbody>-->
            </table>
        </div>
    </div>
</div>
<?php }?>
<?php /*?><?php
if(isset($_REQUEST['rq']) && $_REQUEST['rq'] <> '')
	{
		?>
			<script>
				getService('delete-' + <?php echo $_REQUEST['rq'];?>);				
			</script>
		<?php
	}
?><?php */?>


<?php include_once "includes/foot.php"; ?>
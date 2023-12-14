<?php include_once "ses_set_s.php"; ?>
<?php include_once("includes/head_s.php");
// include_once("RightPanel.php");    
include_once("classes/home_s.class.php");
include_once("classes/dbconst.class.php");
include_once("classes/create_poll.class.php");
include_once("classes/utility.class.php");
$obj_create_poll = new create_poll($m_dbConnRoot,$m_dbConn);
$obj_utility=new utility($m_dbConn);
$requests = $obj_create_poll->getRecordsForMember($_REQUEST['cm']);
//print_r($requests);
?>
<div class="panel panel-info" style="margin-top:6%;margin-left:3.5%; border:none;width:70%">

    <div class="panel-heading" style="font-size:20px">
    <center>  Polls List  </center>
    </div>
  
    <br />
    
     <?php 
	if($_SESSION['role'] && $_SESSION['is_year_freeze'] == 0 && ($_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['profile']['create_poll.php'] == 1))
	{?>
    <center>
	 <button type="button" class="btn btn-primary" onClick="window.location.href='create_poll.php'">Add New Poll</button>
     </center>
<?php }?>

<div class="panel-body">                        
        <div class="table-responsive">
            <table id="example" class="display" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Question</th>
                        <th>Start Date</th>
                        <th>End Date</th>                        
                         <th>View</th>
                         <th>Voted as</th>
                          <th>Status</th>
                                                                                            
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
                        <!--<td><?php echo $requests[$i]['name'];?></td>-->
                         <td><a href="poll_preview.php?rq=<?php echo $obj_utility->encryptData($requests[$i]['poll_id']);?>"><?php echo $requests[$i]['question'];?></a></td>
                        <td><?php echo getDisplayFormatDate($requests[$i]['start_date']);?></td>
                        <td><?php echo getDisplayFormatDate($requests[$i]['end_date']);?></td>
                         <td><a href="poll_preview.php?rq=<?php echo $obj_utility->encryptData($requests[$i]['poll_id']);?>"><img src="images/view.jpg"  width="20"/></a></td>
                         <td><?php echo $requests[$i]['options'];?>
                         <?php 
						 $date = $obj_utility->getDateDiff(getDBFormatDate($requests[$i]["end_date"]), date("Y-m-d"));
						 
							if($date >= 0)
							{?>
                           <td><span style="font-weight:bold; color:#00F;">Active</span></td>
							<?php }
                            else{?>
                              <td>  <span style="color:#F00; font-weight:bold;">Expired !</span></td>
                            <?php }
                            ?>
                                                 	
                         
                         </tr>
                </tbody>
                <?php }?>
            
            <?php }?>
            </table></div>
        </div>
    </div>
</div>

<?php include_once "includes/foot.php"; ?>
<?php
include_once("includes/head_s.php");
include_once("classes/approval_module.class.php");
include_once("includes/dbop.class.php");
$dbConnRoot = new dbop(true);
$obj_approval = new approval_module($m_dbConn,$dbConnRoot);
$display_approval=$obj_approval->FetchAllApprovals();
$ID = "";

?>
<div class="panel panel-info" style="margin-top:6%;margin-left:3.5%; border:none;width:70%">

<div class="panel-heading"id="pageheader" style="width:850px">
         Approvals
    </div>
    <br />
    
    
    <center><a href="approval_module.php" target="_blank"><button type="button" class="btn btn-primary" onClick="window.location.href='approval_module.php'">Add New Approval</button></a></center>
    
       
    <div class="panel-body"> 
    <div class="table-responsive">
    <table id="example" class="display" cellspacing="0" width="100%">
    <thead>
    <tr>
    
    <th>Subject</th>
    <th>Post Date</th>
    <th>Approve By Date</th>
    <th>View</th>
    <th>Voted As</th>
    <th>Status</th>
    </tr>
    </thead>
    <tbody>
     <?php	
            	foreach($display_approval as $k => $v)
           		 {
					// echo "today  ::".date('Y-m-d');
					 //echo "db date  ::".$display_approval[$k]['end_date'];
                 if($display_approval[$k]['end_date'] >= date('Y-m-d'))
					{
						
					 $status ="Active";	
				 	} 
				 else
					{
					 $status = "Expire";
			   }?> 
                <tr align="center">
             		
                	<td><a href="approval_vote.php?id=<?php echo $display_approval[$k]['id'];?>" target="_blank"><?php echo $display_approval[$k]['subject'];?></a></td>
                	<td><?php echo getDisplayFormatDate($display_approval[$k]['post_date']);?></td>
                    <td><?php echo getDisplayFormatDate($display_approval[$k]['end_date']);?></td>
                    
                    <td>  
                    <?php 
					if($display_approval[$k]['end_date'] >= date('Y-m-d'))
					{   
					 ?>                      	
                                <a href="approval_vote.php?id=<?php echo $display_approval[$k]['id'];?>"><img src="images/telegram.png" width="20"/></a>
                                <?php }?>
                				<!--<a id="edit" href="javascript:void(0);" onclick="getNotice(this.id, <?php //echo $display_notices[$key]['id'];?>);"><img src="images/edit.gif" /></a> -->
                			</td>  
                    <td>  
                  	</td>  
                         
                      <td><?php echo $status?></td>              				
                	  
                    </tr>
                    </tr>
                   <?php } ?>
                         	
    </tbody>
    </table>
    </div> 
    </div>
  
</div>

<?php include_once "includes/foot.php"; ?>


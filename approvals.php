<?php
include_once("includes/head_s.php");
include_once("classes/approval_module.class.php");
include_once("includes/dbop.class.php");
$dbConnRoot = new dbop(true);
$obj_approval = new approval_module($m_dbConn,$dbConnRoot);

if(isset($_REQUEST['type']) && $_REQUEST['type'] == "active")
{
	$display_approval=$obj_approval->FetchAllApprovals($_REQUEST['type']);
	//$requests = $obj_request->getRecords($_REQUEST['cm'],$_REQUEST['type']);
}

else if(isset($_REQUEST['type']) && $_REQUEST['type'] == "expire")
{
	$display_approval=$obj_approval->FetchAllApprovals($_REQUEST['type']);
	//$requests = $obj_request->getRecords($_REQUEST['cm'],$_REQUEST['type']);
}


$ID = "";

?>
<div id="middle">
	<br>
	<div class="panel panel-info" id="panel" style="display:none;margin-top:10px;margin-left:2.5%;width:95%;">
	<div class="panel-heading" id="pageheader">Approvals</div>
	<br>
	<br>
    
    
    <center>
    <?php if($_SESSION['role'] && ($_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_MANAGER))
	{?>
    <a href="approval_module.php"><button type="button" class="btn btn-primary" onClick="window.location.href='approval_module.php'">Add New Approval</button></a>
    <?php }?>
    </center>
    
       
    <div class="panel-body"> 
    <div class="table-responsive">
    <ul class="nav nav-tabs" role="tablist">
        
        <li <?php echo (isset($_REQUEST['type']) && $_REQUEST['type'] == "active" && $_REQUEST['type'] <>"expired") ? 'class="active"' : ""; ?>> 
            	<a href="#active" role="tab" data-toggle="tab" onClick="window.location.href='approvals.php?type=active'">Active</a>
    		</li>
            
            <li <?php echo (isset($_REQUEST['type']) && $_REQUEST['type'] == "expire") ? 'class="active"' : ""; ?>>
            	<a href="#expire" role="tab" data-toggle="tab" onClick="window.location.href='approvals.php?type=expire'">Expire </a>
    		</li>
        </ul>
    <table id="example" class="display" cellspacing="0" width="100%">
    <thead>
    <tr>
    <th>Created By</th>
    <th>Subject</th>
    <th>Post Date</th>
    <th>Approve By Date</th>
    <th>Status</th>
    <th>View</th>
    <th>Edit</th>
    <th>Delete</th>
    </tr>
    </thead>
    <tbody>
     <?php	
            	foreach($display_approval as $k => $v)
           		 {
					
                 if($display_approval[$k]['end_date'] >= date('Y-m-d'))
					{
						
					 $status ="Active";	
				 	} 
				 else
					{
					 $status = "Expired";
			   }?> 
                <tr align="center">
             		<td><?php echo  $_SESSION['name'];?></td>
                	<td>
                    <?php if($_SESSION['role'] && ($_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_MANAGER))
						{?>
                    		<a href="approval_view.php?id=<?php echo $display_approval[$k]['id'];?>" target="_blank"><?php echo $display_approval[$k]['subject'];?></a>
                            <?php }
							else
							{?>
								<a href="approval_vote.php?moduleId=<?php echo $display_approval[$k]['id'];?>" target="_blank"><?php echo $display_approval[$k]['subject'];?></a>
							<?php }?></td>
                	<td><?php echo getDisplayFormatDate($display_approval[$k]['post_date']);?></td>
                    <td><?php echo getDisplayFormatDate($display_approval[$k]['end_date']);?></td>
                    <td><?php echo $status?></td>
                    <td>  
                    <?php if($_SESSION['role'] && ($_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_MANAGER))
						{?>
                                	
                                <a href="approval_view.php?id=<?php echo $display_approval[$k]['id'];?>"><img src="images/view.jpg" width="20"/></a>
                          <?php }
						  else
							{?>
								<a href="approval_vote.php?moduleId=<?php echo $display_approval[$k]['id'];?>" target="_blank"><img src="images/view.jpg" width="20"/></a>
							<?php }?>
                			</td>  
                   
                    <?php 
					if($display_approval[$k]['end_date'] >= date('Y-m-d') && $display_approval[$k]['votedcount'] == 0  && $_SESSION['role'] && ($_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['role']==ROLE_ADMIN) )
					{   
					 ?>    
                      <td>                    	
                           <a href="approval_module.php?editid=<?php echo $display_approval[$k]['id'];?>"><img src="images/edit.gif" /></a>
                      </td> 
                      <td>
                      		<a href="approval_module.php?deleteid=<?php echo $display_approval[$k]['id'];?>"><img src="images/del.gif" /></a>
                      </td> 
                    <?php 
					}
					else
					{?>
					  		<td></td>
                            <td></td>
					<?php 
					}?>
                	</td>  
                    <!--<td>   
                     <?php
					 /*if($display_approval[$k]['end_date'] >= date('Y-m-d') && $display_approval[$k]['end_date'] == 0)
					 {?>                         	
                         <a href="approval_module.php?deleteid=<?php echo $display_approvals[$k]['id'];?>"><img src="images/del.gif" /></a>
                     <?php 
					 }
					 else
					 {?>
                    	 <a href="approval_module.php?deleteid=<?php echo $display_approvals[$k]['id'];?>"><img src="images/del.gif" /></a>
					 <?php
					  }*/?>               				
                	  </td>-->
                    </tr>
                    </tr>
                   <?php } ?>
                         	
    </tbody>
    </table>
    </div> 
    </div>
  
</div>
</div>

<?php include_once "includes/foot.php"; ?>


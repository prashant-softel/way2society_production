<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Tasks</title>
</head>
<?php include_once "ses_set_s.php"; ?>
<?php include_once("includes/head_s.php");
include_once("includes/dbop.class.php");
// include_once("RightPanel.php");    
include_once("classes/home_s.class.php");
include_once("classes/dbconst.class.php");
include_once("classes/tasks.class.php");
include_once("classes/utility.class.php");
$dbConnRoot = new dbop(true);
$m_dbConn = new dbop();
$obj_task = new task($m_dbConn);
$obj_utility = new utility($m_dbConn, $dbConnRoot);
//print_r($_SESSION); 

if($_REQUEST['type'] == 'all')
{
	if($_REQUEST['check'] == 'all')
	{
	$display_tasks=$obj_task->FetchAllTasks($_REQUEST['type'],'');
	}
	else
	{
	$display_tasks=$obj_task->FetchAllTasks($_REQUEST['type'],'Raised');
	}
}
else
{
	$display_tasks=$obj_task->FetchAllTasks($_REQUEST['type'],'');	
}
//echo "<pre>";
//print_r($display_tasks);
//echo "</pre>";
$prevID = "";
//print_r($_SESSION);
?>

<div class="panel panel-info" style="margin-top:4%;margin-left:3.5%; border:none;width:90%">
  
    <div class="panel-heading" style="font-size:20px;text-align: center;">
         Tasks
    </div>
    <br />
    <?php
	if($_SESSION['role'] && $_SESSION['is_year_freeze'] == 0 && ($_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_ADMIN_MEMBER || $_SESSION['role']==ROLE_MANAGER))
	
	      {?>

         <center><button type="button" class="btn btn-primary" onClick="window.location.href='addtask.php'">Add New Task</button></center>
          
         <!-- <a href="addnotice.php" >Add New</a>-->
    <?php }?>
	<div class="panel-body">                        
        <div class="table-responsive">
         <ul class="nav nav-tabs" role="tablist">
            <li <?php echo (isset($_REQUEST['type']) && $_REQUEST['type'] == "raised_by") ? 'class="active"' : ""; ?>> 
            	<a href="#home" role="tab" data-toggle="tab" onClick="window.location.href='tasks.php?type=raised_by'"> Assign to me</a>
    		</li>
            <li <?php echo (isset($_REQUEST['type']) && $_REQUEST['type'] == "raised_to") ? 'class="active"' : ""; ?>>
            	<a href="#home" role="tab" data-toggle="tab" onClick="window.location.href='tasks.php?type=raised_to'">Assign by me </a>
    		</li>
             <li <?php echo (isset($_REQUEST['type']) && $_REQUEST['type'] == "all") ? 'class="active"' : ""; ?>>
            	<a href="#home" role="tab" data-toggle="tab" onClick="window.location.href='tasks.php?type=all&check=Raised'">All</a>
    		</li>
         </ul>
         <br />
          <?php if($_SESSION['role'] && $_SESSION['is_year_freeze'] == 0 && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_ADMIN_MEMBER || $_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['role']==ROLE_MANAGER))
	      		{ 
					if($_REQUEST['type'] <> "raised_by" && $_REQUEST['type'] <> "raised_to")
					{?>
						<input type="checkbox" id="raised_check_id" onChange="raised_check()" name="check_list[]" value="2" checked/> <label style="margin-left:4px;margin-top: 4px;">Show Raised</label><br/>
              <?php }
				}
			  ?>
         <?php //echo $_REQUEST['type']; ?>
               <table id="example" class="display" cellspacing="0" width="100%">
               <thead>
            			<tr  height="30" bgcolor="#CCCCCC">

                        <!-- <th>Assigned To Me</th> -->
                      
                        <th style="text-align: center;">Title</th>
                        <th>Priority</th>
                        <th>Due Date</th>
                        <!-- <th>Type</th> 
                        <th>Priority</th>-->
                        <th>Status</th>
                        <th>% Completed</th>
                        <th>Most Recent Comment</th>
                        <!-- <th >View</th> -->
                        <?php if($_SESSION['role'] && $_SESSION['is_year_freeze'] == 0 && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_ADMIN_MEMBER || $_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['role']==ROLE_MANAGER))
	      				{ 
						//echo $_REQUEST['type'];
						if($_REQUEST['type'] == "raised_by")
						{?>
                        <th  style='padding-left:25px;'>Raised By</th>
                        <?php }
						 else if($_REQUEST['type'] == "raised_to")
                        {
                        	?>
                        	<th style='padding-left:40px;' >Raised to</th>
                        	<?php
                        }
						else
						{?>
                        <th style='padding-left:40px;' >Task Owner</th>
                        <th style='padding-left:40px;' >Assigned To</th>
						<?php }
						}
                       if($_REQUEST['type'] == "raised_by")
						{?>
                        <th>Update Status</th> 
                     <?php }?>
					
                    </tr>
                     </thead>
                <tbody>
               
                <?php if($display_tasks <> '')
				{
					//echo "<pre>";
					//print_r($display_tasks);
					//echo "</pre>";
					foreach($display_tasks as $key=>$val)
					{
						//echo "key:".$key;
						//echo "val:".print_r($val);

						//foreach($val as $key=>$val)
						//{
						$sTaskOwnerID = $val['Task_Owner'];
						$taskid=$val['id'];
						//echo //$taskid."    ";
						$sql_latest_comment = "SELECT Comment FROM `comments` where CType = '1' and CRefId = '".$taskid."' order by Id desc LIMIT 1";
						$result_comment = $m_dbConn ->select($sql_latest_comment);
						$latest_comment = $result_comment[0]['Comment'];
						$sql = "SELECT login.name FROM `mapping` join login on mapping.login_id=login.login_id where mapping.id='".$sTaskOwnerID."'";
						$res = $dbConnRoot->select($sql);
						
						$sOwnerName = $res[0]["name"];
						$RaisedBy = $val['RaisedBy'];
						$sql = "SELECT login.name FROM `mapping` join login on mapping.login_id=login.login_id where login.login_id='".$RaisedBy."'";
						$res2 = $dbConnRoot->select($sql);
						$sRaisedBy = $res2[0]["name"];
						
						$Title = "<a href='viewTasks.php?taskId=$taskid'>".$val['Title']."</a>";
						$Priority = $val['Priority'];
						$sPriority = "";
						if($Priority == "1")
						{
							$sPriority = "Low";							
						}
						else if($Priority == "2")
						{
							$sPriority = "Medium";							
						}
						else if($Priority == "3")
						{
							$sPriority = "High";							
						}
						if($Priority == "4")
						{
							$sPriority = "Critical";							
						}
						$id = $val['id'];
						$Status = $val['Status'];
						$sStatus = "";
						if($Status == "1")
						{
							$sStatus = "Raised";							
						}
						else if($Status == "2")
						{
							$sStatus = "Waiting";							
						}
						else if($Status == "3")
						{
							$sStatus = "In Progress";							
						}
						if($Status == "4")
						{
							$sStatus = "Completed";							
						}
						if($Status == "5")
						{
							$sStatus = "Cancelled";							
						}
						$DueDate = $val['DueDate'];
						$PercentCompleted = $val['PercentCompleted'];
						//}	
						$arTask = array("task_id" => $id,"Percentage"=> $PercentCompleted, "Status"=> $Status);

						echo "<tr>";
						//echo "<td>".$sOwnerName."</td>";
						echo "<td>".$Title."</td>";
						echo "<td>".$sPriority."</td>";
						echo "<td>".getDisplayFormatDate($DueDate)."</td>";
						echo "<td>".$sStatus."</td>";
						echo "<td>".$PercentCompleted."</td>";
						echo "<td>".$latest_comment."</td>";
						if($_REQUEST['type'] == "raised_by")
						{
						echo "<td>".$sRaisedBy."</td>";
						}
						else if($_REQUEST['type'] == "raised_to")
						{
						echo "<td>".$sOwnerName."</td>";
						}
						else
						{
							echo "<td>".$sRaisedBy."</td>";
							echo "<td>".$sOwnerName."</td>";
						}
						if($_REQUEST['type'] == "raised_by")
						{
						echo "<td><button type='button' class='btn btn-info btn-sm' data-toggle='modal' data-target='#myModal' onClick='UpdateTaskDlg(".json_encode($arTask).")'>Update</button></td>";
						}
						/*else if($_REQUEST['type'] == "raised_to")
						{ ?>
							
                               <td  valign="middle" align="center"> <a href="addtask.php?id=<?php echo $taskid;?>" style="color:#00F"><img src="images/edit.gif" width="16" /></a></td>
					<?php	} ?>*/
						//echo '<td><a href="#" title="Dismissible popover" data-toggle="popover" data-trigger="focus" data-content="Click anywhere in the document to close this popover">Click me</a><td>';
						echo "</tr>";
                
           			}
				} 
				 ?>                                        			
                </tbody>
            </table>

        </div>
            
</div>
<script type="text/javascript" src="js/bootstrap-modalmanager.js"></script>
<script type="text/javascript" src="js/bootstrap-modal.js"></script>
  <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" style="background-color: #d9edf7;min-height: 0px;padding: 0px">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Update Task Status</h4><input type="hidden" name="task_id" id="task_id" value=""/>
        </div>
        <div class="modal-body">
          <table>
          	<tr style="border-bottom: 1px solid!important">
          		<td style="width: 40%">
		          <p>Status</p>
			      </td>
			      <td>
			          <select name="status" id="status" style=" width:135px; height:20;float: left;">
			                <OPTION VALUE="<?php echo PRIORITY_LOW; ?>" selected>Raised</OPTION>
			                <OPTION VALUE="<?php echo PRIORITY_MEDIUM; ?>">Waiting</OPTION>
			                <OPTION VALUE="<?php echo PRIORITY_HIGH; ?>">In Progress</OPTION>
			                <OPTION VALUE="<?php echo PRIORITY_CRITICAL; ?>">Completed</OPTION>
			                <OPTION VALUE="<?php echo PRIORITY_CRITICAL; ?>">Cancelled</OPTION>
			             </select>
				   	</td>
				</tr>
				<tr>
          		<td  style="width: 40%">
		          <p>Percentage Completed</p>
			      </td>
			      <td><input type="text" name="txtpercentage" id="txtpercentage">
				   	</td>
				</tr>
			</table>
        </div>
        <div class="modal-footer">
        	<button type="button" class="btn btn-default" data-dismiss="modal"  onClick='UpdateTaskProgress()'>Update</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        </div>
      </div>
      
    </div>
  </div>
<!-- <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">Open Modal</button>   -->
</div>
<script type="text/javascript">
<?php 
	if($_REQUEST['type'] == 'all')
	{
		if($_REQUEST['check']=='all')
		{
?>
			document.getElementById("raised_check_id").checked = false;
<?php 
		}
		else
		{
?>
			document.getElementById("raised_check_id").checked = true;
<?php	}
	}
?>
	function raised_check()
	{	
		//alert("hello");
		//salert(document.getElementById("raised_check_id").checked);
		if(document.getElementById("raised_check_id").checked)
		{
			window.location.href='tasks.php?type=all&check=Raised'
		}
		else
		{
			window.location.href='tasks.php?type=all&check=all'
		}
	}
	function UpdateTaskDlg(param)
	{
		document.getElementById("task_id").value = param["task_id"];
		document.getElementById("status").value = param["Status"];
		document.getElementById("txtpercentage").value = param["Percentage"];
	}
	function UpdateTaskProgress()
	{
		var iTaskID = document.getElementById("task_id").value;
		var iStatus = document.getElementById("status").value;
		var sPercentage = document.getElementById("txtpercentage").value;
		$.ajax({
			url: 'ajax/ajaxTask.php',
        	type: 'POST',
        	data: {"TaskID": iTaskID, "status":iStatus, "Percentage": sPercentage,"update":"percent"},
        	success: function(data)
        	{
        		//alert(data);
        		data = data.trim();
        		if(data > 0)
        		{
        			alert("Task updated successfully");
        			window.location.reload();
        		}
            }
		})
	}

</script>
<?php include_once "includes/foot.php"; ?>
 
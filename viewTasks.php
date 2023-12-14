<?php
include_once("includes/head_s.php");
include_once("classes/dbconst.class.php");
include_once("classes/include/dbop.class.php");
include_once("classes/tasks.class.php");
include_once("classes/addComment.class.php");
include_once("classes/utility.class.php");

$obj_comment = new addComment($m_dbConn,$m_dbConnRoot);
$obj_task = new task($m_dbConn,$m_dbConnRoot);
$obj_utility = new utility($m_dbConn,$m_dbConnRoot); 
$taskId = $_REQUEST['taskId'];
//echo $taskId;
$res = $obj_task->selecting($taskId);
$assigned_to_name= $obj_task->getTaskassignedName($res[0]['Task_Owner']);
$FilePath = str_replace('../', '', $res[0]['Attachment']); 	

$commentRes = $obj_comment->getAllComments($taskId);
$priority = "";

if ($res[0]['Priority'] == PRIORITY_LOW)
{
	$priority = "Low";
}
else if ($res[0]['Priority'] == PRIORITY_MEDIUM)
{
	$priority = "Medium";
}
else if ($res[0]['Priority'] == PRIORITY_HIGH)
{
	$priority = "High";
}
else if ($res[0]['Priority'] == PRIORITY_CRITICAL)
{
	$priority = "Critical";
}
$complete = "";
if ($res[0]['Status'] == PRIORITY_LOW)
{
	$complete = "Raised";
}
else if ($res[0]['Status'] == PRIORITY_MEDIUM)
{
	$complete = "Waiting";
}
else if ($res[0]['Status'] == PRIORITY_HIGH)
{
	$complete = "In Progress";
}
else if ($res[0]['Status'] == PRIORITY_CRITICAL)
{
	$complete = "Completed";
}
$arTask = array("task_id" => $res[0]['id'],"Percentage"=> $res[0]['PercentCompleted'], "Status"=> $res[0]['Status']);
$arTaskDate = array("task_id" => $res[0]['id'],"DueDate"=> getDisplayFormatDate($res[0]['DueDate']), "Status"=> $res[0]['Status']);
?>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
    <style>
	#commentTable
	{
		border-right-width:65%;
		width:100%;
	 	border: 2px solid #ddd;
	}
	
  	table.cruises td { 
    border-left: 1px solid #999; 
    border-top: 1px solid #999;  
    padding: 2px 4px;
    }
  	table.cruises tr:first-child td {
    border-top: none;
  }
  table.cruises th { 
    border-left: 1px solid #999; 
    padding: 2px 4px;
    background: #6b6164;
    color: white;
    font-variant: small-caps;
    }
  table.cruises td { background: #eee; overflow: hidden; }
  
  div.scrollableContainer { 
    position: relative;
	width:auto;
    margin: 0px;   
   }
  div.scrollingArea { 
    height: 200px; 
    overflow: auto; 
    }

  table.scrollable thead tr {
    left: -1px; top: 0;
    position: absolute;
    }
	#taComment
	{
		max-width:100%;
	}
	</style>
	<script type="text/javascript" src="js/ajax.js"></script>
    <script type="text/javascript" src="ckeditor/ckeditor.js"></script>
    <script language="JavaScript" type="text/javascript" src="js/validate.js"></script>
    <script language="JavaScript" type="text/javascript" src="js/jsComment.js"></script> 
    <script language="javascript" type="text/javascript">
	function validateForm()
	{
		var comment = document.forms["viewTask"]["comment"].value;
		if(comment == "")
		{
			alert ("Comment must be filled out..");
			return false;
		}
	}
	function clear()
	{
		document.getElementById("comment").value = "";
	}
	</script>
    <script type="text/javascript">
        $(function()
        {
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker({ 
            dateFormat: "dd-mm-yy", 
            showOn: "both", 
            buttonImage: "images/calendar.gif", 
            buttonImageOnly: true 
        })});
		
			
		function uploadimage()
		{
			var val = CKEDITOR.instances['description'].getData();
		    var uploaded_fileName = document.getElementById('userfile').value;
					//alert(uploaded_fileName.length);
					if(!(val.length > 0))
					{ 
					//var msgText = 'Dear Member, <br /> <br /> Please find attachment : ' + document.getElementById('subject').value + ' <br /> <br /> Thanking you, <br />' + document.getElementById('issueby').value;						
					var msgText = 'Dear Member, <br /> <br /> ';
						if(uploaded_fileName.length != "")
						{
							msgText += 'Please find attachment : ' + document.getElementById('subject').value + ' <br /> <br />';
						}
						msgText += 'Thanking you, <br />' + document.getElementById('issueby').value;						
					CKEDITOR.instances['description'].setData(msgText);
					$("#description").val(msgText);

					}
		}
	    
  </script>
	</head>
	<body>
		<div id="middle">
			<div class="panel panel-info" id="panel" style="display:block; margin-top:6%;width:77%;">
      			<div class="panel-heading" id="pageheader"><button type="button" class="btn btn-primary" onClick="window.location.href='tasks.php?type=raised_to'" style="float: left">Go Back</button>
                Task Details</div>
				<form name="viewTask" id="viewTask" method="post" action="process/addComment.process.php" enctype="multipart/form-data" onSubmit="return validateForm()">
                 	<table width="100%">
						<tr>
                        	<td width="50%">
                            	<table align='center' width="100%">
                                	<tr>
                                    	
                                    </tr>
                    				<tr>
                						<td style="text-align:right;width:50%;vertical-align:center;padding-top:1%"><b>Raised By &nbsp;:&nbsp;</b></td>
										<td width="50%" style="padding-top:1%"><?php echo $obj_task->getLoginName($res[0]['RaisedBy']);?>
                        					<br><input type="hidden" name="tId" id="tId" value="<?php echo $res[0]['id']?>"/>
                                      	</td>
                					</tr>
                                    	<tr>
                						<td style="text-align:right;width:50%;vertical-align:center;padding-top:1%"><b>Assiged To &nbsp;:&nbsp;</b></td>
										<td width="50%" style="padding-top:1%"><?php echo $assigned_to_name;?>
                                        </td>
                					</tr>
                                    
                    				<tr>
                						<td style="text-align:right;width:50%;vertical-align:center;padding-top:1%"><b>Title&nbsp;:&nbsp;</b></td>
										<td width="50%" style="padding-top:1%"><?php echo $res[0]['Title']; ?><br></td>
                					</tr>
        							<tr>
										<td style="text-align:right;width:50%;vertical-align:center;padding-top:1%"><b>Description &nbsp;:&nbsp;</b></td>
										<td width="50%" style="padding-top:1%"><?php echo $res[0]['Description'];?><br></td>
									</tr>
                					<tr>
                						<td style="text-align:right;width:50%;vertical-align:center;padding-top:1%"><b>Priority&nbsp;:&nbsp;</b></td>
										<td width="50%" style="padding-top:1%"><?php echo $priority;?><br>
                    					</td>
                					</tr>
                					<tr>
                						<td style="text-align:right;width:50%;vertical-align:center;padding-top:1%"><b>Due Date &nbsp;:&nbsp;</b></td>
										<td width="50%" style="padding-top:1%"><?php echo getDisplayFormatDate($res[0]['DueDate'])?>
                    					<button type='button' class='btn btn-info btn-sm' data-toggle='modal' data-target='#myModaldate' onClick='UpdateTaskDueDateDlg(<?php echo json_encode($arTaskDate)?>)' style="padding:1%;margin-left:5%">Extend</button><br>
                    					</td>
                					</tr>
                					<tr>
                						<td style="text-align:right;width:50%;vertical-align:center;padding-top:1%"><b>Completion Status &nbsp;:&nbsp;</b></td>
										<td width="50%" style="padding-top:1%"><?php echo $complete?>
                    					<br>
                    					</td>
                					</tr>
                            		<tr>
                						<td style="text-align:right;width:50%;vertical-align:center;padding-top:1%"><b>Completion Percentage &nbsp;:&nbsp;</b></td>
										<td width="50%" style="padding-top:1%"><?php echo $res[0]['PercentCompleted']?>%
                    					 <button type='button' class='btn btn-info btn-sm' data-toggle='modal' data-target='#myModal' onClick='UpdateTaskDlg(<?php echo json_encode($arTask)?>)' style="padding:1%;margin-left:5%">Update</button><br>
                    					</td>
                					</tr>
                                    <?php if(!empty($res[0]['Attachment']))
									{ ?>
									<tr>
                						<td style="text-align:right;width:50%;vertical-align:center;padding-top:1%"><b>Attach Document &nbsp;:&nbsp;</b></td>
										<td width="50%" style="padding-top:1%"><?php echo end(explode('/',$res[0]['Attachment']))?>
                    					 <button type='button' class='btn btn-info btn-sm btn-xs' data-toggle='modal' data-target='#myDocumentModal' style="padding:1%;margin-left:5%">View</button><br>
                    					</td>
                					</tr>
									
									<?php
                                    }?>
                                    
                                </table>
                            </td>
                            <td width="50%">
                            	<table width="100%">
                                	<?php if(sizeof($commentRes) > 0)
									{
									?>
                            		<tr>
                            			<td colspan="2" style="text-align:center"><b>-:&nbsp; Comments &nbsp; :-</b></td>
                            		</tr>
                             		<?php
									}
									?>
                            		<tr>
                            			<td colspan="2">
                                			<div class="scrollableContainer">
        										<div class="scrollingArea">
                                					<table id = "commentTable" style="text-align:center;">
                            						<?php
														$align = "left";
														for($i = 0;$i < sizeof($commentRes);$i++)
														{
															$FilePath1 = str_replace('../', '', $commentRes[$i]['Attachment']);
															//echo $FilePath1;
															if($commentRes[$i]['PostedBy'] == $_SESSION['login_id'])
															{
																$align = "right";
															}
															else
															{
																$align = "left";
															}
													?>
                            							<tr>
                                    						<td style="text-align:<?php echo $align;?>;vertical-align:center;width:36%;padding-top:1%" colspan="2"><b><?php if($commentRes[$i]['PostedBy'] == $_SESSION['login_id']){ echo "You";}else{echo $commentRes[$i]['Name'];}?></b></td>
                                						</tr>
                                						<tr>
                                                        	<?php
																$len = strlen($commentRes[$i]['Comment']);
																//echo $len;
																$rows = intval($len/90);
															?>
                                							<td style="width:5%;text-align:<?php echo $align;?>;">
                                                            <?php
									 							if($commentRes[$i]['PostedBy'] == $_SESSION['login_id'])
									 							{
									  						?>
                                        					<?php 
																	$checkTime = array();
																	$ts=$commentRes[$i]['Diff'];
																	$checkTime = explode(":",$ts);
																	$timeStamp = array();
																	$timeStamp =  explode(" ",$commentRes[$i]['TimeStamp']);
																	
																	if(intval($checkTime[1]) < 5 && $checkTime[0] < 1)
																	{
                                        					?>
                                            							<a id="delBtn" onClick="deleteComment(<?php echo $commentRes[$i]['Id'];?>,<?php echo $taskId;?>)"><img src="images/can.png" alt="delete" style="width:10%;height:10%;padding-bottom:7%"></img></a>
                                    						<?php
																	}
															?>
                                        					<?php
									 							}
                                    							else
                                     							{
																}
									 							?>
                                                                <?php
																$r1 = 0;
																if($rows < 2)
																{
																	$r1 = 3;
																}
																else
																{
																	$r1 = $rows;
																}
																?>
                                                            <textarea id="taComment" cols="50" rows = "<?php echo $r1;?>" name="comment<?php echo $i+1;?>" id="comment<?php echo $i+1;?>" style="background-color:<?php if($commentRes[$i]['PostedBy'] == $_SESSION['login_id']){ echo "#C5F7AC";}else{}?>" readonly><?php echo $commentRes[$i]['Comment']?></textarea>
															
                                                                <br>
																<?php if($commentRes[$i]['Attachment']<> '')
																{ ?> 	
																	<span style="text-align: left; float:left"><a onclick="commentimgage(' <?php echo $FilePath1?>' )" data-toggle='modal' data-target='#myDocumentModal'><img src="images/attview.png" alt="view" style="width:12%;height:12%;margin-left: 38%;"></img></a></span>
																<?php }?>
																	<span><?php echo getDisplayFormatDate($timeStamp[0])." ".$timeStamp[1];?></span>
                                                            </td>
                                    					</tr>
                            						<?php
														}
													?>
                            						</table>
                                    			</div>
                                    		</div>
                            			</td>
                            		</tr>
                                </table>
                            </td>
                        </tr>                    
                    </table>
                    <center>
                    <table width="60%">
                    	<tr>
                            <td style="text-align:right;width:50%;vertical-align:center;padding-top:1%"><b>Comment &nbsp;:&nbsp;</b></td>
							<td width="50%" style="padding-top:1%;"><textarea id="comment" name="comment" rows="7" cols="70" placeholder="Enter comment here.."></textarea> </td>
                    	</tr>
						<tr>			
						 	<td style="text-align:right;width:50%;vertical-align:center;padding-top:1%"><b>Attachment &nbsp;:&nbsp;</b></td>
                      		<td><input name="userfile" id="userfile" type="file" /> <a id="noticename" 
							style="visibility:hidden;" target="_blank"> View Attachment </a></td>
						</tr>
                        <tr>
                           	<td style="text-align:right;width:50%;vertical-align:center;padding-top:1%"><input type="submit" id="btnSubmit" name="btnSubmit" value = "Add Comment" class="btn btn-primary"></td>
  							<td style="text-align:right;width:50%;vertical-align:center;padding-top:1%"><input type="reset" id="btnCancel" name="btnCancel" value = "Cancel" class="btn btn-primary">
                    <br><br></td>
                     	</tr>
                    </table>
                   	</center>                 		
				</form>
       		</div>
		</div>
        
        <div class="modal fade" id="myDocumentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              <h4 class="modal-title" style="padding: 0px;">Task Document</h4>
            </div>
            <iframe class="geturlTask" src="#" name="livePreviewFrame" width="100%" height="500px" frameborder="0" noresize="noresize" style=" min-height: 100%;"> </iframe>
            <div class="modal-footer">
              <!--<span class="btn btn-success"  data-dismiss="modal" onclick="SOPreadDone()">Done</span>-->
              <span class="btn btn-success"  data-dismiss="modal">Done</span>
            </div>
          </div>
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
			                <OPTION VALUE="<?php echo PRIORITY_LOW; ?>">Raised</OPTION>
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
  
  
  <!--Modal for Extending due date -->
  <div class="modal fade" id="myModaldate" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" style="background-color: #d9edf7;min-height: 0px;padding: 0px">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Extend Task Due Date</h4><input type="hidden" name="task_id" id="task_id" value=""/>
        </div>
        <div class="modal-body">
          <table>
          	<tr style="border-bottom: 1px solid!important">
          		<td style="width: 60%">
		          <p>Due Date</p>
			      </td>
			      <td><input type="text" name="due_date" id="due_date"   class="basics" size="10"   style="width:80px;"/><input type="hidden" name="task_id" id="task_id"    size="10"   style="width:80px;"/></td>
				</tr>
				
			</table>
        </div>
        <div class="modal-footer">
        	<button type="button" class="btn btn-default" data-dismiss="modal"  onClick='UpdateDueDate()'>Extend</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
             
        </div>
      </div>
      
    </div>
  </div>
  <!-- End  of due date modal -->
<!-- <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">Open Modal</button>   -->
</div>
<script type="text/javascript">
	function UpdateTaskDlg(param)
	{
		document.getElementById("task_id").value = param["task_id"];
		document.getElementById("status").value = param["Status"];
		document.getElementById("txtpercentage").value = param["Percentage"];
	}
	function UpdateTaskDueDateDlg(param)
	{
		console.log('param',param);
		document.getElementById("due_date").value = param["DueDate"];
		document.getElementById("task_id").value = param["task_id"];
		//console.log(document.getElementById("task_id").value);
		
	}
	function UpdateTaskProgress()
	{
		var iTaskID = document.getElementById("task_id").value;
		var iStatus = document.getElementById("status").value;
		var sPercentage = document.getElementById("txtpercentage").value;
		$.ajax({
			url: 'ajax/ajaxTask.php',
        	type: 'POST',
        	data: {"TaskID": iTaskID, "status":iStatus, "Percentage": sPercentage,"update" :"percent"},
		success: function(data)
        	{
        		
            }
		});
		alert("Task updated successfully");
        window.location.reload();
		
        			
	}
	function UpdateDueDate()
	{
		var iTaskID = document.getElementById("task_id").value;
		var idue_date = document.getElementById("due_date").value;
		$.ajax({
			url: 'ajax/ajaxTask.php',
        	type: 'POST',
        	data: {"TaskID": iTaskID,"DueDate": idue_date,"update" : "due"},
        	success: function(data)
        	{
        		console.log('data',data);
            }
		});
		alert("Task updated successfully");
        window.location.reload();
	}
	$(document).ready(function(e) {
        Task_Url = "<?php echo $FilePath; ?>";
		///alert("Bingo");
		if(Task_Url != null && Task_Url != "")
		{
			console.log("Task_Url",Task_Url);
			$('.geturlTask'). attr("src",Task_Url);	
		}
    });
	function commentimgage(url)
	{
		
		if(url != null && url != "")
		{
			console.log("Task_Url",url);
			$('.geturlTask'). attr("src",url);	
		}	
	}
</script>
	</body>
</html>
<?php include_once "includes/foot.php"; ?>
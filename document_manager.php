
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
<title>W2S - Nomination Form Manager</title>
</head>





<?php include_once "ses_set_s.php"; ?>
<?php include_once("includes/head_s.php");
//include_once("RightPanel.php");    
include_once("classes/home_s.class.php");
include_once("classes/dbconst.class.php");
include_once("classes/document_manager.class.php");
//include_once("classes/include/dbop.class.php");

//$obj_templates = new doc_templates($m_dbConn,$m_dbConnRoot);

$obj_doc_manager = new document_manager($m_dbConn,$m_dbConnRoot);

if(isset($_REQUEST['type']))
{
	$requests = $obj_doc_manager->getRecords($_REQUEST['type']);
	//$meeting_requests = $obj_doc_manager->getMeeting();
}

?>
<style>
    .link{display:inline}
    .link {float: left}
	.disabled {
   pointer-events: none;
   cursor: default;
}
</style>
<div class="panel panel-info" style="margin-top:3%;margin-left:3.5%; border:none;width:90%">
 
    <div class="panel-heading text-center" style="font-size:20px">
     Nomination Form Manager
    </div>
    <br />
    <br />
    <div class="panel-body">                        
        <div class="table-responsive">
                    <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li <?php echo (isset($_REQUEST['type']) && $_REQUEST['type'] == "submitting") ? 'class="active"' : ""; ?>> 
            	<a href="#home" role="tab" data-toggle="tab" onClick="window.location.href='document_manager.php?type=submitting'"> Submitting by member</a>
    		</li>
            <li <?php echo (isset($_REQUEST['type']) && $_REQUEST['type'] == "submitted") ? 'class="active"' : ""; ?>>
            	<a href="#home" role="tab" data-toggle="tab" onClick="window.location.href='document_manager.php?type=submitted'">Received by society </a>
    		</li>
            <li <?php echo (isset($_REQUEST['type']) && $_REQUEST['type'] == "approved") ? 'class="active"' : ""; ?>>
            	<a href="#home" role="tab" data-toggle="tab" onClick="window.location.href='document_manager.php?type=approved'">Approved by management</a>
    		</li>
            <li <?php echo (isset($_REQUEST['type']) && $_REQUEST['type'] == "cancel") ? 'class="active"' : ""; ?>>
            	<a href="#home" role="tab" data-toggle="tab" onClick="window.location.href='document_manager.php?type=cancel'">Cancelled by management</a>
    		</li>
        </ul>
		<br/>
                                   
            <table id="example" class="display" cellspacing="0" width="100%">
                <thead>
                                 
                    <tr>
                        <th>Nomination ID</th>
                       <th>Unit No</th>
                        <th>Owner Name</th>
                        <th>Status</th>
                       <?php 
						if($_REQUEST['type'] == "submitting")
						{ 
						?>
                        	<th> Date & Time</th>
						<?php } 
                        else if($_REQUEST['type'] == "submitted")
						{ ?>  <th>Received on</th>
						<?php } 
						else if($_REQUEST['type'] == "approved"){
							 ?> <th>Approved on</th>
							<?php }
							else
							{?>
								<th>Cancel on</th>
							<?php } ?>
						
                        <?php 
						if($_REQUEST['type'] == "submitting")
						{ 
						?>
                        	<th>Update</th>
						<?php } 
						else if($_REQUEST['type'] == "submitted")
						{ ?>  <th>Update</th>
						<?php }
						 	else if($_REQUEST['type'] == "cancel")
						{ ?>  <th></th>
						<?php }
						else{
							 ?> <th>Meeting</th>
							<?php } 
						if($_REQUEST['type'] == "approved")
						{?>  
                          <th></th>
							<?php } ?>
                    </tr>
                </thead>
                <tbody>

                <?php 
				//$check_memberID="";
				for($i = 0; $i < sizeof($requests); $i++)
				{
				?>
					  <td style="padding-left:50px;"><?php echo $requests[$i]['nomination_id'];?></td>
                	 <?php $nomination_id= $requests[$i]['nomination_id'];?>
                     <td style="padding-left:25px;"><?php echo $requests[$i]['unit_no'];?></td>	
                    <td><?php echo $requests[$i]['owner_name'];?></td>	
					<?php $nomination_id =$requests[$i]['nomination_id'];
						  $nomination_status=$requests[$i]['nomination_status']; ?>          		
                 	<td>
					<?php if($requests[$i]['nomination_status'] === '1')
						  { 
							echo "Submitting";
						  }
				 		  else if($requests[$i]['nomination_status'] === '2')
						  { 
						    echo "Submitted";
					      }
						 else if($requests[$i]['nomination_status'] === '3') 
						 { 
						   echo "Approved"; 
						 }
						 else if($requests[$i]['nomination_status'] === '4') 
						 { 
						   echo "  Cancel"; 
						 }?>
                     </td>
                     <td><?php echo $requests[$i]['timestamp'];?></td>
                     <?php if($requests[$i]['nomination_status'] === '3')
					 { ?>  
                     <td style="padding-left:25px;"><?php echo $requests[$i]['meeting'];?></td>
					 <?php	 }?>
              		 <td>
					 <?php 

						if($_REQUEST['type'] == "submitting" || "submitted" && $_REQUEST['type']!="approved" && $_REQUEST['type']!="cancel")
			  			{ 
							$nominees = $requests[$i]['Nomineedetails'];
							
							$nominee_name="";
							
							for($iCnt = 0; $iCnt < sizeof($nominees); $iCnt++)
						    {
								 $nominee_name = $nominee_name . $nominees[$iCnt]['nominee_name']." : ". $nominees[$iCnt]['percentage_share']."%   ";	
							}?>                          
                            	<button id="popup_button" type='button' class='btn btn-info btn-sm' data-toggle='modal' data-target='#myModal' onClick='getDetails("<?php echo $nomination_id;?>" ," <?php echo $requests[$i]['owner_name']?>","<?php echo $nominee_name ;?>","<?php echo  $requests[$i]['nomination_status'];?>","<?php echo $requests[$i]['member_id']?>")'>Update</button>
             		 </td>
			  			<?php } ?> 		 
              			<script type="text/javascript" src="js/bootstrap-modal.js"></script>
             			<script type="text/javascript" src="js/bootstrap-modalmanager.js"></script>
                	</tr>
						<?php }?>
      			</tbody>
            </table>
        </div>
        <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" style="background-color: #d9edf7;min-height: 0px;padding: 0px">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <?php if($nomination_status=='1')
			{?>
          <h4 class="modal-title text-center">
          Receiving Nomination Form</h4>
          <?php }
       	else if($nomination_status=='2')
			{?>
          <h4 class="modal-title text-center">Approving Nomination Form</h4>
          <?php }?>
        </div>
        <div class="modal-body">
          
          	 <?php 

				//{?><div style="font-size:12px;"><!--<form method="post" action="class/document_manager.class.php">-->
                <table><tr><td><b>Nomination No</b></td><td>:</td><td><input type="text" disabled name="id" id="id" value=""  style="border: none;box-shadow: none;width:"/></td></tr>
                <tr><td><b>Owner Name</b></td><td>:</td><td><input type="text" name="name" disabled id="name" value="" style="border: none;box-shadow: none;"/><br></td></tr>
                
                <tr><td><b>Nominee Name</b></td><td>:</td><td><input type="text" name="nominee_name" disabled id="nominee_name" value="" style="width:400px;border: none;box-shadow: none;"/><br></td></tr>
                <?php if($nomination_status=='2')
			{?>
             <!--   <tr><td></td><td></td><td style="color:
                blue;"><b>Please select meeting in which your nomination form is approved!<b><br></td></tr>
				
                
                <tr><td></td><td></td><td><select required  id="status" onchange="getStatus()">
				                       <?php// echo $combo_doc =$obj_doc_manager->combobox("select id,Title from meeting",'0'); ?>
				</select><br></td></tr>-->
               
                <tr><td><b>NOTICE</b></td><td>:</td><td><input type="text" name="" disabled id="" value="Please confirm all the following details before clicking 'Approved' button." style="width:400px;border: none;box-shadow: none;color:blue;"/>
                
<textarea rows="2" cols="50" id="approved" style="color:red;border:none;box-shadow: none;resize:none;font-size:15px;display:none;overflow:hidden">
Your nomination form is already approved if you still click on 'approved' button then your previous form will be cancelled.
</textarea></td></tr>
                <?php }
				
				else if($nomination_status=='1')
				{?>
                
                <tr><td><b>NOTICE</b></td><td>:</td><td><input type="text" name="" disabled id="" value="Please confirm all the following details before clicking 'Received' button." style="width:400px;border: none;box-shadow: none;color:blue;"/><br><br>
<textarea rows="2" cols="50" id="received" style="color:red;border:none;box-shadow: none;resize:none;font-size:15px;display:none;overflow:hidden">
We have already 'Received' your nomination form.
</textarea>
                
                </td>
                </tr>
                <?php }?>
             </table><!--</form>--></div>		
        </div>
        <div class="modal-footer">
        <input type="hidden" id="niminee" name="niminee">
      		<?php if($nomination_status=='1')
			{
				?>
               <?php //echo $check_memberID;?>
        	<button type="submit" class="btn btn-default" data-dismiss="modal"  id="id" onClick='nominationUpdate("<?php echo $nomination_status;?>","<?php echo $check_memberID;?>")'>Received</button>
            <?php }
			else if($nomination_status=='2')
			{ 
			?>
           <?php echo $check_memberID;?>
        	<button type="submit" class="btn btn-default" data-dismiss="modal"  id="id" value="0" onClick='nominationUpdate("<?php echo $nomination_status;?>","<?php echo $check_memberID;?>")'>Approved</button>
            <?php }?>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <?php //}?>
        </div>
      </div>
      
    </div>
  </div>
<script>
var check_id;
function getDetails(nomination_id,owner_name,nominee_name,nomination_status,member_id)
{
	 	

	 document.getElementById("id").value=nomination_id;
	 

	 check_id=member_id;
	//alert(check_id);
	 
	 document.getElementById("name").value=owner_name;
	 
	 document.getElementById("nominee_name").value=nominee_name;
	
	// alert(nominee_name);
	//alert(owner_name);
		// alert(nomination_id);
	 
	// document.getElementById("update").value=nomination_id;
	//	document.getElementById("member_id").value=member_id;
	$.ajax({
	url : "ajax/ajaxdocument_manager.ajax.php",
	type : "POST",
	dataType : "json",
	data: {"method": "checkExitingMember", "nomination_status":nomination_status,"member_id":check_id},
	success: function(data)
	{
		//alert("Successcheck details");
		if(data=='0')
		{
			if(nomination_status=='1')
			{
		document.getElementById("received").style.display='none';
			}
			else if(nomination_status=='2')
			{
		document.getElementById("approved").style.display='none';
			}
		}
		else if(data=='1')
		{
		document.getElementById("received").style.display='block';
		}
		else if(data=='2')
		{
		document.getElementById("approved").style.display='block';
		}
		
	}
	});

/*	if(member_id>1)
	{
		alert("You are already submitted nomination form with following details \n nomination ID  "+ nomination_id + "\n Owner name "+ owner_name );
		}*/
	
}

	 //alert("owner_name"+owner_name);

</script>
<script>
var status="";

function getStatus()
{
	status=document.getElementById("status").value;

}







function nominationUpdate(nominationtype)
{	
 var nomination_id=document.getElementById("id").value;
 //var check_id=document.getElementById("member_id").value;
	//alert(" sdd");
	check_id=check_id;
	//alert(check_id);
	
/*   if(status=="" && nominationtype=="1" || status!="" && nominationtype=="2"){*/
	$.ajax({
	url : "ajax/ajaxdocument_manager.ajax.php",
	type : "POST",
	dataType : "json",
	data: {"method": "updateNomination", "nomination_id":nomination_id,"check_id":check_id},
	success: function(data)
	{
		//alert("Success");
		if(data=='success')
		{	
			if(nominationtype=="1")
			{
			alert("Nomination form recieved successfully");
			window.location.reload();
			}
			else if(nominationtype=="2"){
			alert("Nomination form approved successfully");
			window.location.reload();
		}}
		else if(data=='failed')
		{
			alert("update failed");
		}
	}
	});
}/*else if(nominationtype=="2")
{
	alert("Please select meeting then click on the received button");
	
	}}*/
</script>
         
      </div>
</div>


<?php include_once "includes/foot.php"; ?>
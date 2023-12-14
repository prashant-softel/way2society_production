<?php
include_once("includes/head_s.php");
include_once ("classes/dbconst.class.php");
include_once("classes/include/dbop.class.php");
include_once("classes/approval_module.class.php");
//include_once("classes/utility.class.php");

$obj_approvals=new approval_module($m_dbConn, $m_dbConnRoot);
$AccessDetails=$obj_approvals->getAccessForApproval($_REQUEST['moduleId']);
$details=$obj_approvals->ApprovalVote($_REQUEST['moduleId']);
$attacment= $obj_approvals->View_attachments($_REQUEST['moduleId']);

$takefeedback= $obj_approvals->TakeFeedback($_REQUEST['moduleId'],$AccessDetails[0]['mem_other_family_id'] );
if($details[0]['end_date'] >= date('Y-m-d'))
{
	$status ="Active";	
} 
else
{
	$status = "Expired";
}
?>

<html>
<head>
<link rel="stylesheet" type="text/css" href="css/pagination.css" >
<style>
.opt,.ques
{
	border:0px solid;
	border-color:#3399CC;
	padding: 10px;
    font-size: 12px;
	text-transform: capitalize;
}
</style>

</head>
<div id="middle">
<br>
<div class="panel panel-info" id="panel" style="display:none;margin-top:10px;margin-left:2.5%;width:95%;">
<div class="panel-heading" id="pageheader">Vote For Approval</div>
<br>
<br>
<center><button type="button" class="btn btn-primary" onClick="window.location.href='approvals.php?type=active'">Go Back</button></center>
<div class="panel-body">

<center>
 <?php if(sizeof($AccessDetails) <> '')
  {?>
  <table width="85%" style=" border:1px solid #e7e7e7;" align="center">
 
  	<tr style="background-color:#bce8f1; height:30px">
  		<th colspan="4" style="text-align: left;line-height: 30px;font-size: 14px;padding-left: 10px;">Subjects</th>
  	</tr>
  	<tr>
  		<td colspan="4" align="left" style="padding-bottom: 10px;padding-top: 10px;padding-left: 10px;padding-right: 10px;font-size: 12px;"><?php echo $details[0]['subject'];?></td>
  	</tr>
  	<tr style="background-color:#bce8f1; height:30px">
  		<th  colspan="4" style="text-align: left;line-height: 30px;font-size: 14px;padding-left: 10px;">Descriptions</th>
  	</tr>
  	<tr>
  	<td colspan="4" style="padding-bottom: 10px;padding-left: 10px;padding-right: 10px;padding-top: 10px;text-align: justify;">
  	<?php echo $details[0]['description'];?>
  	</td>
  		<!--<td colspan="3" style="padding-bottom: 10px;padding-left: 10px;padding-right: 10px;padding-top: 10px;text-align: justify;">
    Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of "de Finibus Bonorum et Malorum" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, "Lorem ipsum dolor sit amet..", comes from a line in section 1.10.32.
  		</td>-->
  	</tr>
  	<tr style="background-color:#bce8f1; height:30px">
  		<th style="text-align: left;line-height: 30px;font-size: 14px;padding-left: 10px;width: 40%;">Attachments</th>
  		<th style="text-align: center;line-height: 30px;font-size: 14px;padding-left: 10px;width: 25%;">Voting line starting on</th>
  		<th style="text-align: center;line-height: 30px;font-size: 14px;padding-left: 10px;width: 25%;">Voting line closes on</th>
  		<th style="text-align: center;line-height: 30px;font-size: 14px;padding-left: 10px;width: 10%;">Status</th>
  	</tr>
  	<tr>
  		<td style="padding-left: 10px;padding-bottom: 5px;padding-right: 10px;padding-top: 5px;">
  	<table>
  	<?php 
  
  	if(sizeof($attacment) > 0)
  	{
	  for($i=0;$i<sizeof($attacment); $i++)
	  {?>
		  
			<tr><td><b><a href="<?php echo $attacment[$i]['Url'];?>" target="_blank"><?php  echo $attacment[$i]['Doc_name'];?></a></td></tr>          
	  <?php }
  	}
 	 else
  	{?>
	  <tr><td>No Attachment</td></tr>
  <?php }
  ?>
  
  	<!--<tr><td>noc</td></tr>
  	<tr><td>noc</td></tr>-->
  	</table>
  </td>
  
  
  <td style="text-align:center"><?php echo getDisplayFormatDate($details[0]['post_date']); ?></td>
  <td style="text-align:center"><?php echo getDisplayFormatDate($details[0]['end_date']);?></td>
  <td style="text-align:center"><?php echo $status;?></td>
  </tr>
  <tr style="background-color:#bce8f1; height:30px">
  <th colspan="4" style="text-align: center;line-height: 30px;font-size: 14px;padding-left: 10px;width: 50%;">Feedback</th>
  </tr>
  
  
  <tr>
  <td colspan="4" >
  	<center>
    <div id="voteField" style="display:block;">
  	<table style="width:60%;text-align: center;border: 1px solid #e0dcdc; ">
    	<tr><td><br></td></tr>
  		<tr >
  			<td  align="left" class="opt" style="padding-left: 70px;"><input type="radio" style="font-size:18px;background-color:transparent; box-shadow: 0px 0px 0px #666;    width: 4%; height: .9em;" name="answer" id="option" class="answer" <?php  echo $selected; ?> value="1"></label><span style="text-transform: capitalize; font-size:14px;">&nbsp;&nbsp;&nbsp;  Yes </span>
        	</td>
 		</tr>
    	<tr>
    		<td  align="left" class="opt" style="padding-left: 70px;">
        	<input type="radio" style="font-size:18px;background-color:transparent; box-shadow: 0px 0px 0px #666;    width: 4%; height: .9em;" name="answer" id="option" class="answer" <?php  echo $selected; ?> value="2"></label><span style="text-transform: capitalize; font-size:14px;">&nbsp;&nbsp;&nbsp;  No </span>
        	</td>
    	</tr>
    	<tr><td><br></td></tr>
    	<tr>
    		<td>
    		<textarea id="feedback" name="feedback" cols="60" rows="5"></textarea>
    		</td>
    	</tr>
    	<tr><td><br></td></tr>
   	 <tr>
    		<td>
       		 <input type="hidden" id="C_memberId" name="C_memberId" value="<?php echo $AccessDetails[0]['mem_other_family_id'];?>">
        	<input type="hidden" name="approvalId" id="approvalId" value="<?php echo $_REQUEST['moduleId'];?>">
        	<input type="submit" name="insert" id="insert" value="Vote" class="btn btn-primary" style="color:#FFF; width:100px; background-color:#337ab7;" onClick="submitFeedback();"/>
    		</td>
   		 </tr>
   
    
    <tr><td><br></td></tr>
    
  </table>
 	</div>
    <div id="show_feedback" style="display:none;">
   <table  style="width:60%;text-align: center;border: 1px solid #e0dcdc;"  >
    <?php 
	if($takefeedback[0]['approval_status'] <> 0)
	{?>
   	<?php 
		if($takefeedback[0]['approval_status'] == 1)
		{
			$vote = 'Yes';
		}
		else
		{
			$vote = 'No';
		}
		//echo $vote;
	?>
   
    <tr>
    <td align="center" style="width: 500px;padding-bottom: 10px;padding-top: 10px;"><b>Your Selection was : <?php echo $vote ;?></b></td></tr>
    <tr>
    <td align="left" style="padding-left: 20px;padding-right: 20px;"><b>Comments : </b></td></tr>
	<tr><td align="left" style="padding-left: 20px;padding-right: 20px;padding-bottom: 10px;"><?php echo $takefeedback[0]['comments'] ?></td>
    </tr>
    
    

    <?php } ?>
    </table>
    </center>
    </td></tr>
  </center>
  <tr>
  <?php } 
	else
	{?>
   <p style="font-size: 15px;color: red;"><b>You are not authrized person for approval !</b></p>
	<?php }?>
  </tr>
  </table>
  </center>
  </div>
  </div>
  </div>
  
 <script type="application/javascript">
 var uservote = '<?php echo $takefeedback[0]['approval_status'] ?>';
 $(document).ready(function(){
	// alert(uservote);
	 if(uservote > 0)
	 {
  		document.getElementById("show_feedback").style.display='block';
		document.getElementById('voteField').style.display='none';
	 }
	 else
	 {
		document.getElementById("show_feedback").style.display='none';
		document.getElementById('voteField').style.display='block'; 
		//document.getElementById('voteField').style.width='60%';
		//document.getElementById('voteField').style.border='1px solid rgb(224, 220, 220)';
	 }
	});
 function submitFeedback()
 {
	
	 var approvalId = document.getElementById('approvalId').value;
	 var comments=document.getElementById('feedback').value;
	 var login_id = '<?php echo $_SESSION['login_id']?>';
	 var CommetteeId = document.getElementById('C_memberId').value;
	 if(document.querySelector('input[name="answer"]:checked') !=null)
	 {
		var selectOption=document.querySelector('input[name="answer"]:checked').value;
		$.ajax({
				
			url : "ajax/approval_module.ajax.php",
			type : "POST",
			data : {"method" : 'Feedback',"approvalID" : approvalId, "comments" : comments, "login_id" : login_id ,"selectOption": selectOption,"CommetteeId" :CommetteeId},
			success : function(data)
			{	
				console.log(data);
				var a		= data.trim();
				var result	= new Array();
				var result	= a.split("@@@");
				//alert(result[1]);
				if(result[1] == 1)
				{
					alert("Your Feedback Successfully Updated");
					//document.getElementById('feedback').style.display='block';
				}
				else
				{
					alert("Your Feedback Not Updated!");
					//document.getElementById('feedback').style.display='none';
				}	
				 location.reload(true);	
				
			},
		});
		
		
	 }
	 else
	{
		alert("Please select option");	
	}
	 
 }
 </script> 
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
<!--<div style="width: 60%; border-radius:4px" class="panel panel-default">
  <div class="panel-heading"><span style="font-size:22px">
	<?php 
		for($i=0;$i<sizeof($details);$i++){
	?>
 <?php echo $details[$i]['subject'];?></span>
<?php } ?>
</div>
<div style="padding-top: 20px; width:95%;" class="">
<div id="option_div" style="display:;">
<table  width="100%" align=center border=1  cellpadding=5 class="normaltext" style=" BORDER-COLLAPSE: collapse; padding:5px;border-radius:4px;border:none">
<tbody>
<tr>
<td  style="padding-top: 0px;  float: left;margin-left: 120px; font-weight: bold; ">Voting line closes on &nbsp; : &nbsp;<span style="color:#00F"><?php echo getDisplayFormatDate($details[0]['end_date']) ;?></span></td>
</tr>

<tr>
<td style=" font-size: 15px;float: middle;margin-center: 15px;"><center><b><?php echo $details[0]['description']?></b></center></td>
</tr>
<tr>
<td align="left" class="opt">
<input type="radio" style="font-size:18px;background-color:transparent; box-shadow: 0px 0px 0px #666;    width: 4%; height: .9em;" name="answer" id="option" class="answer" <?php  echo $selected; ?>></label><span style="text-transform: capitalize; font-size:14px;">&nbsp;&nbsp;&nbsp;  Yes </span></td>
</tr>
<tr><br/></tr>
<tr>
<td align="left" class="opt">
<input type="radio" style="font-size:18px;background-color:transparent; box-shadow: 0px 0px 0px #666;    width: 4%; height: .9em;" name="answer" id="option" class="answer" <?php  echo $selected; ?>></label><span style="text-transform: capitalize; font-size:14px;">&nbsp;&nbsp;&nbsp;  No </span></td>
</tr>
</tbody>
</table>
<br/>
<input type="submit" name="insert" id="insert" value="Vote" class="btn btn-primary" style="color:#FFF; width:100px; background-color:#337ab7;" onClick="voteForma(0);"/>
<p>&nbsp;</p>
</div>

</div>
    </center>    
    </div>
    </div>-->
</html>



























<?php include_once "includes/foot.php"; ?>
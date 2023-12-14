<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Meeting</title>
</head>

<?php include_once "ses_set_s.php"; ?>
<?php
if(isset($_SESSION['admin']))
{
  include_once("includes/header.php");
}
else
{
  include_once("includes/head_s.php");
}
?>
<?php
include_once("classes/meeting.class.php");
$obj_meeting = new meeting($m_dbConn,$m_dbConnRoot);
$meetingCount = $obj_meeting->getCountOfMeeting();


?> 

<html>
  <head>
    <title>meeting</title>
    <link rel="stylesheet" type="text/css" href="css/pagination.css" >
      <script type="text/javascript" src="js/validate.js"></script>
      <script type="text/javascript" src="js/populateData.js"></script>
      <script type="text/javascript" src="js/ajax.js"></script>
      <script type="text/javascript" src="js/meeting.js"></script>
      <script type="text/javascript" src="js/CreateMeeting.js"></script>
   	  <script type="text/javascript">
       $(function()
        {
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker
			({ 
            	dateFormat: "dd-mm-yy", 
            	showOn: "both", 
            	buttonImage: "images/calendar.gif", 
            	buttonImageOnly: true, 
				yearRange: '-10:+10', // Range of years to display in drop-down,
        	})
		});
	</script>
      <style>
        select.dropdown
        {
          position: relative;
          width: 100px;
          margin: 0 auto;
          padding: 10px 10px 10px 30px;
          appearance:button;
          /* Styles */
          background: #fff;
          border: 1px solid silver;
          /* cursor: pointer;*/
          outline: none;
        }
        @media print
        {    
          .no-print, .no-print *
          {
            display: none !important;
          }
          div.tr, div.td , div.th 
          {
            page-break-inside: avoid;
          }
        }
		#hide
        {
          display: none;
		  /*text-align: center;*/
        }
		/* Style the tab */
		<style>
		body {font-family: Arial;}

/* Style the tab */
		.tab 
		{
			float:left;
    		border: 1px solid #ccc;
			background-color:#337ab7;
		}

/* Style the buttons inside the tab */
		.tab button 
		{
    		background-color: inherit;
    		float: left;
    		border: 1px thin white;
    		outline: none;
    		cursor: pointer;
    		padding: 14px 16px;
    		transition: 0.3s;
    		font-size: 17px;
		}

/* Change background color of buttons on hover */
		.tab button:hover
		{
    		background-color: #ddd;
			color:#000000;
		}

/* Create an active/current tablink class */
		.tab button.active
		{
    		background-color: #ccc;
		}

/* Style the tab content */
	.tabcontent
	{
    	display: none;
    	padding: 6px 12px;
    	border: 1px solid #ccc;
    	border-top: none;
	}
    </style>
  </head>
  <body>
    <center>
        <br>
        <div class="panel panel-info" id="panel" style="display:none">
          <div class="panel-heading" id="pageheader">Meeting</div>
            <form name="meeting" id="meeting" method="post" action="process/meeting.process.php">
              <br>
              <div style="margin-right: 5px;float:right;"><button type="button" class="btn btn-primary" onClick="window.location.href='createMeeting.php'" style="">Create New Meeting</button>&nbsp;&nbsp;&nbsp;
                <button type="button" class="btn btn-primary" onClick="window.open('momGroup.php','_blank')" style="" >Manage Resident Groups</button>

              </div>

            	<div class="panel-body">
                	<div class="table-responsive">
  						<ul class="nav nav-tabs" role="tablist">
            				<li <?php echo (isset($_REQUEST['type']) && $_REQUEST['type'] == "open") ? 'class="active"' : ""; ?>> 
            					<a href="#home" role="tab" data-toggle="tab" onClick="window.location.href='meeting.php?type=open'">Open <font color='#FF0000'><sup><?php echo $meetingCount['0']?></sup></font></a>
    						</li>
            				<li <?php echo (isset($_REQUEST['type']) && $_REQUEST['type'] == "invited") ? 'class="active"' : ""; ?>>
            					<a href="#profile" role="tab" data-toggle="tab" onClick="window.location.href='meeting.php?type=invited'">Invited <font color='#FF0000'><sup><?php echo $meetingCount['1']?></sup></font></a>
    						</li>
                            <li <?php echo (isset($_REQUEST['type']) && $_REQUEST['type'] == "inProcess") ? 'class="active"' : ""; ?>> 
            					<a href="#home" role="tab" data-toggle="tab" onClick="window.location.href='meeting.php?type=inProcess'">Minutes Pending <font color='#FF0000'><sup><?php echo $meetingCount['2']?></sup></font></a>
    						</li>
                            	<li <?php echo (isset($_REQUEST['type']) && $_REQUEST['type'] == "closed") ? 'class="active"' : ""; ?>> 
            					<a href="#profile" role="tab" data-toggle="tab" onClick="window.location.href='meeting.php?type=closed'">Minuted <font color='#FF0000'><sup><?php echo $meetingCount['3']?></sup></font></a>
    						</li>
                            <li <?php echo (isset($_REQUEST['type']) && $_REQUEST['type'] == "cancel") ? 'class="active"' : ""; ?> >
            					<a href="#home" role="tab" data-toggle="tab" onClick="window.location.href='meeting.php?type=cancel'">Cancelled <font color='#FF0000'><sup><?php echo $meetingCount['4']?></sup></font></a>
    						</li>
        				</ul>
					</div>	
          	  </div>
          	</form>
             <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document" style="width: 65%; ">
    <div class="modal-content">
      <div class="modal-header">
        <!--<h5 class="modal-title" id="exampleModalLabel">Send Email</h5>-->
         <div class="modal-title" id="exampleModalLabel" style="font-size: 16px;"><b>Send Email</b></div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div style="width:100%">
      <label style="line-height: 20px;width: 15%;">Selected Group &nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;</label>
      <select name="grpname" id="grpname" >
         		<option value ="AO">All Owners</option>
         		<option value ="ACO">All Co-Owners</option>
         		<option value="AOCO">All Owners & Co-Owners</option>
         		<option value="AR">All Residents</option>                                            
         		<option value="ACM">All Committee Members</option>
         		<option value="AVO">All Car Owners</option>
         		<option value="AT">All Tenants</option>
         		</select>
      </div>
       <div style="width:100%">
      <label style="line-height: 20px;width: 15%;">Additional Email&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</label>
      <textarea id="endText" name="endText" cols='35' rows='2'></textarea>
      </div>
      <div style="width: 50%;margin-right: -14%; color: red;"><b>Note :</b> Enter multiple emails sending on other members using camma seprators.(abc@way2society.com,xyz@way2society.com)</div>
      
      <hr>
      <div style="width:100%"><label style="line-height: 20px;"><b> Meeting Name &nbsp;&nbsp;:&nbsp;&nbsp; <span id="m_title"></span></b></label>
      <!--<div id="m_title" style="font-size: 15px;"></div>-->
      </div>
      <div style="width:100%; height:250px; overflow: auto;">
      <div id="minutesDetails"></div>
      </div>
     
                           
            
        
      </div>
      <div class="modal-footer">
      <input type="hidden" id="metting_id" value="0">
      <input type="hidden" id="grp_id" value="0">
      <input type="hidden" id="title" value="0">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Send Email</button>
      </div>
    </div>
  </div>
</div> 
      	</div>
            <div style="width:70%">
            <?php
            if(isset($_REQUEST['type']) && $_REQUEST['type'] == "open")
			{
				$type="open";
				$res = $obj_meeting->pgnation($type);
				echo $res;
			}
			if(isset($_REQUEST['type']) && $_REQUEST['type'] == "invited")
			{
				$type="invited";
				$res = $obj_meeting->pgnation($type);
				echo $res;
			}	
			if(isset($_REQUEST['type']) && $_REQUEST['type'] == "inProcess")
			{
				$type="inProcess";
				$res = $obj_meeting->pgnation($type);
				echo $res;
			}
			if(isset($_REQUEST['type']) && $_REQUEST['type'] == "closed")
			{
				$type="closed";
				$res = $obj_meeting->pgnation($type);
				echo $res;
			}
			if(isset($_REQUEST['type']) && $_REQUEST['type'] == "cancel")
			{
				$type="cancel";
				$res = $obj_meeting->pgnation($type);
				echo $res;
			}
			?>
            </div>
            
           
        
   
</center>
</body>
</html>
<?php include_once "includes/foot.php"; ?>
<script>
var member_arr =[];
var minutesDetails ='';
var Title ='' ;
function SendEamils(mID)
{
	//alert ("cancelMeeting :"+mID);
	var GroupId ='';
	$.ajax
	({
		url : "ajax/meeting.ajax.php",
		type : "POST",
		//datatype: "JSON",
		data : {"method":"GetData","mId":mID},
		success : function(data)
		{
			//console.log(data);	
			var a    = data.trim();
			var arr1	= new Array();
			var arr2	= new Array();
			var arr3	= new Array();
			var time    = new Array();		
			arr1		= a.split("@@@");
			//console.log(JSON.parse(arr1[1]));
			jsonData = JSON.parse(arr1[1]); 
			//console.log(jsonData.Id);
			document.getElementById("m_title").innerHTML='<b>'+jsonData.Title+'</b>';
			document.getElementById("metting_id").value=jsonData.Id;
			document.getElementById("grp_id").value=jsonData.GroupId;
			document.getElementById("title").value=jsonData.Title;
			Title = jsonData.Title;
			GroupId = jsonData.GroupId;
			getMember(GroupId);
		}	
	});

   $.ajax({
			url : "ajax/minutesOfMeeting.ajax.php",
			//url : "ajax/meeting.ajax.php",
			type : "POST",
			datatype: "JSON",
			data : {"method":"viewMinutes","mId":mID},
			success : function(data)
			{
				//alert ("data:"+data);
				minutesDetails =data;
				document.getElementById("minutesDetails").innerHTML = data;	
				
			}
		});
		
}
function getMember(GroupId)
{
	 $.ajax({
			url : "ajax/meeting.ajax.php",
			type : "POST",
			datatype: "JSON",
			data : {"method":"fetchMemberEmail","gId":GroupId},
			success : function(data)
			{
				//console.log(data);
				var a    = data.trim();
				var arr1	= new Array();	
				arr1		= a.split("@@@");
				var memData = JSON.parse(arr1[1]);
				console.log(memData.length);
				//member_arr.push(JSON.parse(arr1[1]));
				$.ajax({
					url : "ajax/meeting.ajax.php",
					type : "POST",
					//datatype: "JSON",
					data : {"method":"SendingEmails","minutesDetails":minutesDetails,"title":Title,"mdata":memData},
					success : function(data)
					{
						var a    = data.trim();
						var arr1	= new Array();	
						arr1		= a.split("@@@");
						var res = arr1[1];
						if(res == 1)
						{
							alert("Email send successfully");
						}
						else
						{
							alert("Email sending failed, try again");
						}
					}
				});
				/*var mem_email ='';
				var mem_name = '';
				console.log("Above for lp", memData.length);
				for(var i=0; i< memData.length; i++)
				{
					console.log("inside for lp");
					mem_name =memData[i]['member_name'];
					mem_email =memData[i]['member_email'];
					console.log("mem_name :"+mem_name+ "mem_email"+mem_email);
					 
				}*/
			}
		});
}
console.log("Mem arr",member_arr);
</script>
<?php 
//$meetingRes = $obj_cMeeting->getMeetingByMeetingId($mId);
//$meetingRes = json_decode($meetingRes,true);?>
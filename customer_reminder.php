<?php
	include_once("includes/head_s.php"); 
	include_once("classes/dbconst.class.php");
	include_once("classes/customer_reminder.class.php");
	include_once("classes/viewcustomerreminder.class.php");
	$obj_cus_rem=new CustomerReminder($m_dbConn,$m_dbConnRoot);
	$TargetName=$obj_cus_rem->getTargetName();
	
?>
<html>
<head>
<style>
.table {
    border-radius: 5px;
    width: 50%;
    margin: 0px auto;
    float: none;
}

</style>
<script>
document.title='Way2Society/Reminders';
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
  </script>
  
   <script type="text/javascript">
    $( document ).ready(function()
	{
		var method="<?php echo $_REQUEST['method'];?>";
		var Id;
		if( method == "edit" )
		{
			Id = "<?php echo $_REQUEST['Id']; ?>";

			$.ajax
			({
		
				url: "ajax/CustRemType.ajax.php",
				type: "POST",
				datatype: "JSON",
				data: {"method":"getCustdetails","Id":Id},
				success: function(data)
				{
					console.log(data);
					var a=data.trim();
					var arr2=new Array();
					arr2=a.split("#");
					document.getElementById('custid').value=arr2[0];
					document.getElementById('title').value=arr2[1];
					document.getElementById('frequency').value=arr2[6];
					document.getElementById('daterem').value=arr2[7];
					document.getElementById('descp').value=arr2[2];
					//document.getElementById('target').value=arr2[8];
					var m=JSON.parse(arr2[10]);
					var display=document.getElementById('target');
					var a=new Array();
					var c=new Array();
					for(var i=0;i<m.length;i++)
					{
						
						for(k=0;k<display.options.length;k++)
						{
							

							if(display.options[k].value==m[i])
							{
								display.options[k].selected=true;
								a.push(display.options[k].text);
								console.log(a);
							}
							
							
						}

					}
					c.push(a);
					var msg=a.join("\n");
					console.log(msg);
					document.getElementById('descptest').value=msg;
					document.getElementById('number').value=arr2[9];
					for(var i=0;i<m.length;i++)
					{
						document.getElementById('target').selected=m[i];
					}
					document.getElementById('sms').value=arr2[3];
					if(document.getElementById('sms').value==1)
					{
						document.getElementById('sms').checked=true;
					}
					else
					{
						document.getElementById('sms').checked=false;
					}
				document.getElementById('email').value=arr2[4];
				if(document.getElementById('email').value==1)
					{
						document.getElementById('email').checked=true;
					}
					else
					{
						document.getElementById('email').checked=false;
					}
				document.getElementById('mnot').value=arr2[5];
				if(document.getElementById('mnot').value==1)
					{
						document.getElementById('mnot').checked=true;
					}
					else
					{
						document.getElementById('mnot').checked=false;
					}
				
					document.getElementById("btnSubmit").value = "Update";
					
					/*document.getElementById("btnSubmit").onclick=function()
					{
						window.location.href="viewcustomerreminder.php";
					}*/

				}
				
				
			});
		}
	});

    
    </script>
    <script type="text/javascript">
    	function check()
		{
			var text = $('#target option:selected').toArray().map(item => item.text).join('\n');
			document.getElementById('descptest').value=text;
			
		}
    </script>
    <script type="text/javascript">
    	$(document).ready(function() {
 			 $('#number').bind("cut copy paste drag drop", function(e) {
     		 e.preventDefault();
 		 });     
		});
	function isNumberKey(evt) {
    	var charCode = (evt.which) ? evt.which : evt.keyCode;
    	if (charCode > 31 && (charCode < 48 || charCode > 57))
        		return false;
    return true;
	}
    </script>
   <script type="text/javascript">
$(document).ready(function () {
    $('#btnSubmit').click(function() {
      checked = $("input[type=checkbox]:checked").length;

      if(!checked) {
        alert("You must check at least one checkbox.");
        return false;
      }

    });
});

</script>

</head>
<body>
<form name="reminder" id="reminder" method="post" action="process/addCustomerReminder.process.php"  enctype"multipart/form-data">
<div class="panel panel-info" id="panel" style="width:100%;display:block;">
<?php if($_REQUEST['method']=='edit'){?>
<div class="panel-heading" style="font-size:20px;text-align:center;">
	Update Reminder
</div>
<?php } 
 else{?>
<div class="panel-heading" style="font-size:20px;text-align:center;">
	Add New Reminder
</div>
<?php }?>
<br/>
 <div class="table-responsive">
    <table >     
<table id="" class="mx-auto">
<tr>
<td>
<table style="margin-left:10%" >
<tr><td><br/></td></tr>
<tr><td><br/></td></tr>
<tr>
<input type="hidden" name="reminderid" id="reminderid" value="<?php echo $_REQUEST['Id'];?>">
<input type="hidden" name="custid" id="custid">
<td align="left"><b>Title</b><span style="color:#F00">*</span></td>
<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
<td><textarea name="title" id="title" rows="3" cols="25"></textarea></td>
<td></td>
<td align="left" style="padding-left:180px"><b>Frequency</b><span style="color:#F00">*</span></td>
<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
<td><select id="frequency" name="frequency" required>
<option value="">Select</option>
<option value="1">One Time</option>
<option value="2">Daily</option>
<option value="3">Weekly</option>
<option value="4">Monthly</option>
<option value="5">Quarterly</option>
<option value="6">Half Yearly</option>
<option value="7">Yearly</option>
</select></td>

</tr>
<tr><td><br/></td></tr>
<tr><td><br/></td></tr>
<tr>
<td align="left"><b>Description</b></td>
<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
<td><textarea name="descp" id="descp" rows="6" cols="25"></textarea></td>
<td></td>
<td align="left" style="padding-left:180px"><b>Target Group</b><span style="color:#F00">*</span></td>
<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
<td><select id="target" name="target[]" multiple="multiple" style="width:170px;height:200px" onChange="check();" required>
<?php for($i=0;$i<sizeof($TargetName);$i++)
{ 
	$targetnme=$TargetName[$i]['gnme'];
	$targetid=$TargetName[$i]['gid'];
?>
	<option value="<?php echo $targetid; ?>"><?php echo $targetnme; ?></option>

<?php } ?>
</select>&nbsp;&nbsp;<a href="createGrp.php?method=create" target="_blank"><button type="button" class="btn btn-primary" id="addgrp" name="addgrp">Add Group</button></td></a>


</tr>
<tr><td><br/></td></tr>
<tr>
<td align="left"><b>Event Date</b><span style="color:#F00">*</span></td>
<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
<td>
<input type="text" name="daterem" id="daterem" class="basics" size="30" readonly  style="width:120px;" required/>
</td>
<td></td>
<td align="left" style="padding-left:180px"><b>Selected Target</b></td>
<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
<td><textarea name="descptest" id="descptest" rows="6" cols="20" readonly></textarea></td>
</tr>

<tr><td><br/></td></tr>

<tr>
<td align="left"><b>Reminder to be sent before</b><span style="color:#F00">*</span></td>
<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>

<td><input type="text" name="number" id="number" onkeypress="return isNumberKey(event)" placeholder="Enter Numeric value only" required><span style="color:#F00">*Please enter 0 if no reminder to be set before*</span></td>
<td></td>
<td align="left" style="padding-left:180px"><b>Notify By</b><span style="color:#F00">*</span></td>
<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
<td width="70%">
<table cellspacing="10">

<tr>
<td><input type="checkbox" id="sms" name="sms" style="margin-top:10%"></td>
<td><b>SMS</b></td>
<td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>

<tr>
<td><input type="checkbox" id="email" name="email"  style="margin-top:10%;"></td>
<td><b>EMAIL</b></td>
<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
</tr>

<tr>
<td><input type="checkbox" id="mnot" name="mnot" style="margin-top:10%"></td>
<td><b>MOBILE_Notification</b></td>
</tr>
</table>
</tr>

</table>
</td>
</tr>
<tr><td><br/></td></tr>
<tr><td><br/></td></tr>

</table>
<input type="submit" id="btnSubmit" name="btnSubmit" value="Submit" class="btn btn-primary" style="margin-left:42%;width:10%"/>
</td>
</tr>
</table>
<br/>
<br/>

</div>
</div>
</form>
</body>
</html>














<?php include_once "includes/foot.php"; ?>

<?php
	include_once("includes/head_s.php"); 
	include_once("classes/dbconst.class.php");
	include_once("classes/viewcustomerreminder.class.php");
	$objrem=new SMSReminder($m_dbConn,$m_dbConnRoot);
	$obj_utility = new utility($m_dbConn,$m_dbConnRoot);
	
?>

<html>
<head>
<script type="text/javascript" src="js/viewcustomerreminder.js"></script>
<script type="text/javascript">
function deleteProcessCustRemType(str)
{
	//alert(str);
	$.ajax
	({
		url: "ajax/CustRemType.ajax.php",
		type: "POST",
		datatype: "JSON",
		data: {"method":"deleteProcessCustremdetails","Id":str},
		success: function(data)
		{
			alert ("Data deleted Successfully");
			var a=data.trim();
			if(a == "1")
			{
				window.location.href = "viewcustomerreminder.php?type=process";
			}
			else
			{
				alert ("Problem in deleting the selected record.");
			}
			
		}
		
	});
}	
</script>
<script>
document.title='Way2Society/Reminders';
</script>
</head>
<body>
<div class="panel panel-info" id="panel" style="width:100%;display:block;">
<div class="panel-heading" style="font-size:20px;text-align:center;">
	 Reminders
</div>
<br/>
<center><button type="button" class="btn btn-primary" onClick="window.location.href='customer_reminder.php'">Add New Reminder</button></center>
<br/>
<div class="table-responsive">
         <ul class="nav nav-tabs" role="tablist">
            <li <?php echo (isset($_REQUEST['type']) && $_REQUEST['type'] == "active") ? 'class="active"' : ""; ?>> 
            	<a href="#home" role="tab" data-toggle="tab" onClick="window.location.href='viewcustomerreminder.php?type=active'"> Active</a>
    		</li>
            <li <?php echo (isset($_REQUEST['type']) && $_REQUEST['type'] == "process") ? 'class="active"' : ""; ?>>
            	<a href="#home" role="tab" data-toggle="tab" onClick="window.location.href='viewcustomerreminder.php?type=process'"> Processed </a>
    		</li>
         </ul>
         <br/>
 <?php if($_REQUEST['type']=='active'){ ?>
			<center><div style="width:100%">
            <?php
				echo $objrem->getSMSdata();				
 			?>
            </div>
            </center>
			<?php }
			else if($_REQUEST['type']=='process'){?>
            	<center><div style="width:100%">
            <?php
				echo $objrem->getSMSProdata();				
 			?>
            </div></center><?php }?>
            
            </div>

</div>
</body>


</html>



















<?php include_once "includes/foot.php"; ?>

<?php
include_once("../../classes/defaults.class.php");
include_once("../../classes/include/dbop.class.php");
$m_dbConn = new dbop();
$obj_defaults = new defaults($m_dbConn);
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Way2Society - Update Tables</title>
<script type="text/javascript" language="javascript" src="js/jquery.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
	function Send_Balance_Sheet_Details()
	{
		var startNo = document.getElementById('start').value.trim();
		var endNo = document.getElementById('end').value.trim();
		var beginDate = document.getElementById('beginDate').value.trim();
		var endDate = document.getElementById('endDate').value.trim();
		if(startNo.length > 0 && endNo.length > 0)
		{
			document.getElementById('msg').innerHTML = 'Message Log : <br />Checking Balance_Sheet. Please Wait...';
			$.ajax({
				url : "script/Chk_Balance_Sheet.script.php",
				type : "POST",
				data: { "start": startNo, 
						"end": endNo,
						"beginDate": beginDate,
						"endDate": endDate
					 },
				
				success : function(data)
				{
					document.getElementById('msg').innerHTML += data;
				},
			});
		}
		else
		{
			document.getElementById('msg').innerHTML = 'Message Log : <br />Please enter all fields.';
		}
	}
	
	function ExportToExcel()
	{
		 var myBlob =  new Blob( [$("#msg").html()] , {type:'application/vnd.ms-excel'});
		 var url = window.URL.createObjectURL(myBlob);
		 
		 var a = document.createElement("a");
		 document.body.appendChild(a);
		 a.href = url;
		 a.download = "Mismatch.xls";
		 a.click();
		//adding some delay in removing the dynamically created link solved the problem in FireFox
		 setTimeout(function() {window.URL.revokeObjectURL(url);},0);
	}
	
</script>



			

</head>
<body>
	<h3>Select the Database to Update</h3>
	<p>NOTE : Select same Start and End name if only single DB is to be updated.</p>
	<b>Start DB Name : </b>hostmjbt_society&nbsp;<input type="text" name="start" id="start" />
	<br /><br />
	<b>End DB Name &nbsp;&nbsp;: </b>hostmjbt_society&nbsp;<input type="text" name="end" id="end" />
	<br /><br />
  <!--  <b>Select Year For &nbsp;&nbsp;:  &nbsp;&nbsp;<select name="default_year" id="default_year">
            	<?php
					//echo $combo_year = $obj_defaults->combobox("select YearID, YearDescription from year where status = 'Y' and YearID >='".$_SESSION['society_creation_yearid']."' ORDER BY YearID DESC", $_SESSION['default_year'], "Please Select"); 
                ?>		
            </select>-->
            
    <b>Begin Date &nbsp;&nbsp;: </b> &nbsp;<input type="text" name="beginDate" id="beginDate" />
    <br/><br/>       
    <b>End Date &nbsp;&nbsp;: </b> &nbsp;<input type="text" name="endDate" id="endDate" />
	<br /><br />
    <input type="button" value="Submit" onclick="Send_Balance_Sheet_Details();" />
	<br /><br />
    <div id="msg" style="color:#F00;">Message Log : <br /></div>
</body>
</html>
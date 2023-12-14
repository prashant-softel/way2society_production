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
	function updateLedgerRound()
	{
		var dbNo = document.getElementById('start').value.trim();
		if(dbNo != '')
		{
			document.getElementById('msg').innerHTML = 'Message Log : <br />Updating Ledger . Please Wait...';
			$.ajax({
				url : "script/UpdateLedgerRoundOff.script.php",
				type : "POST",
				data: { "dbNo": dbNo, 
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
	<b>DB Name : </b>hostmjbt_society&nbsp;<input type="text" name="start" id="start" />
    <br><br>
    <input type="button" value="Update" onclick="updateLedgerRound();" />
	<br /><br />
    <div id="msg" style="color:#F00;">Message Log : <br /></div>
</body>
</html>
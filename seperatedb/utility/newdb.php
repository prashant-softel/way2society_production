<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Way2Society - Update Tables</title>
<script type="text/javascript" language="javascript" src="js/jquery.js"></script>
<script>
	function initDB()
	{
		var startNo = document.getElementById('start').value.trim();
		var endNo = document.getElementById('end').value.trim();
		
		if(startNo.length > 0 && endNo.length > 0)
		{
			document.getElementById('msg').innerHTML = 'Message Log : <br />DB initialization in process. Please Wait...';
			$.ajax({
				url : "script/newdb.script.php",
				type : "POST",
				data: { "start": startNo, 
						"end": endNo},
				success : function(data)
				{
					//alert(data);
					document.getElementById('msg').innerHTML += data;
				},
				fail: function()
				{
					
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) 
				{
					
				}
			});
		}
		else
		{
			document.getElementById('msg').innerHTML = 'Message Log : <br />Please enter all fields.';
		}
	}
</script>
</head>
<body>
	<h3>Select the New Database Created to Add Default Table and Data</h3>
	<p>NOTE : Select same Start and End name if only single DB is to be Initialized.</p>
	<b>Start DB Name : </b>hostmjbt_society&nbsp;<input type="text" name="start" id="start" />
	<br /><br />
	<b>End DB Name &nbsp;&nbsp;: </b>hostmjbt_society&nbsp;<input type="text" name="end" id="end" />
	<br /><br />
	<input type="button" value="Submit" onclick="initDB();" />
	<br /><br />
	<div id="msg" style="color:#F00;">Message Log : <br /></div>
</body>
</html>
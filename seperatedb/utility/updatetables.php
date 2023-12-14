<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Way2Society - Update Tables</title>
<script type="text/javascript" language="javascript" src="js/jquery.js"></script>
<script>
	function updateTables()
	{
		var startNo = document.getElementById('start').value.trim();
		var endNo = document.getElementById('end').value.trim();
		var query = document.getElementById('query').value.trim();
		
		//alert(startNo + ":" + endNo + ":" + query);
		
		if(startNo.length > 0 && endNo.length > 0 && query.length > 0)
		{
			document.getElementById('msg').innerHTML = 'Message Log : <br />Updating DB. Please Wait...';
			$.ajax({
				url : "script/updatetables.script.php",
				type : "POST",
				data: { "start": startNo, 
						"end": endNo, 
						"query": query},
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
	function ExportToExcel()
	{
		 var myBlob =  new Blob( [$("#msg").html()] , {type:'application/vnd.ms-excel'});
		 var url = window.URL.createObjectURL(myBlob);
		 
		 var a = document.createElement("a");
		 document.body.appendChild(a);
		 a.href = url;
		 a.download = "QueryReuslt.xls";
		 a.click();
		//adding some delay in removing the dynamically created link solved the problem in FireFox
		 setTimeout(function() {window.URL.revokeObjectURL(url);},0);
	}
	
</script>
</head>
<body>
	<h3>Select the Database to Update</h3>
	<h4 style="color:#0000CC;"><b>Please update database <font style="color:#FF0000;font-size:20px;">hostmjbt_society0</font> while adding/updating/deleting any column in DB.</b></h4>
	<h4 style="color:#0000CC;"><b><a href="viewdb.php" target="_blank">Click Here</a> to view the DB count that needs to be updated.</b></h4>
	<p>NOTE : Select same Start and End name if only single DB is to be updated.</p>
	<b>Start DB Name : </b>hostmjbt_society&nbsp;<input type="text" name="start" id="start" />
	<br /><br />
	<b>End DB Name &nbsp;&nbsp;: </b>hostmjbt_society&nbsp;<input type="text" name="end" id="end" />
	<br /><br />
	<h3>Enter Query</h3>
	<b>Query : </b><input type="text" name="query" id="query" style="width:800px;" />
	<br /><br />
	<input type="button" value="Submit" onclick="updateTables();" />
	<br /><br />
	<div id="msg" style="color:#F00;">Message Log : <br /></div>
</body>
</html>
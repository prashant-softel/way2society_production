<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Way2Society - Update Tables</title>
<script type="text/javascript" language="javascript" src="js/jquery.js"></script>
<script>
	function updateTables(requestType)
	{
		var society_id = document.getElementById('soc_id').value.trim();

		if(society_id > 0)
		{
			$.ajax({
				url : "script/DeleteDupCodeFromMap.php",
				type : "POST",
				data: { "soc_id": society_id, "request_type":requestType},
				success : function(data)
				{
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
	
	function ExportToExcel() // Export the Report Which Mapping we going to delete
	{
		 var myBlob =  new Blob( [$("#msg").html()] , {type:'application/vnd.ms-excel'});
		 var url = window.URL.createObjectURL(myBlob);
		 var a = document.createElement("a");
		 document.body.appendChild(a);
		 a.href = url;
		 a.download = "Duplicate_Mapping_Code.xls";
		 a.click();
		//adding some delay in removing the dynamically created link solved the problem in FireFox
		 setTimeout(function() {window.URL.revokeObjectURL(url);},0);
	}
	
</script>
</head>
<body>
	<h3>Enter society id to delete duplicate mapping code</h3>
	<b>society ID : </b>&nbsp;<input type="text" name="soc_id" id="soc_id" />
	<br /><br />
	<input type="button" value="Delete Duplicate Code"  onclick="updateTables(1);" />
    <input type="button" value="Validate" onclick="updateTables(0);" />
	<br /><br />
	<div id="msg" style="color:#F00;">Message Log : <br /></div>
</body>
</html>
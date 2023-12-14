<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Way2Society - Find the corrupted entry due to reconciliation</title>
<script type="text/javascript" language="javascript" src="js/jquery.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
	function findCorruptedEntryDueToReconciliation()
	{
		var startNo = document.getElementById('start').value.trim();
        var endNo = document.getElementById('end').value.trim();
		if(startNo != '' && endNo != '')
		{
			document.getElementById('msg').innerHTML = 'Message Log : <br />Finding Invalid Opening Date . Please Wait...';
			$.ajax({
				url : "script/findCorruptedEntryDueToReconciliation.script.php",
				type : "POST",
				data: { "startNo": startNo, 'endNo':endNo
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
	<h3>Select the Database to Find the corrupted entry due to reconciliation</h3>
    <p>NOTE : Select same Start and End name if only single DB is to be updated.</p>
	<b>Start DB Name : </b>hostmjbt_society&nbsp;<input type="text" name="start" id="start" />
	<br /><br />
	<b>End DB Name &nbsp;&nbsp;: </b>hostmjbt_society&nbsp;<input type="text" name="end" id="end" />
	<br /><br />
    <input type="button" value="Search" onclick="findCorruptedEntryDueToReconciliation();" />
	<br /><br />
    <div id="msg" style="color:#F00;">Message Log : <br /></div>
</body>
</html>
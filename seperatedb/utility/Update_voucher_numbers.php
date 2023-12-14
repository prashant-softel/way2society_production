<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Way2Society - Update Tables</title>
<script type="text/javascript" language="javascript" src="js/jquery.js"></script>
<script>
	function updateTables()
	{
		var dbName = document.getElementById('dbname').value.trim();
		var sVoucherNo = document.getElementById('s_voucherno').value.trim();
		var sVoucherID = document.getElementById('s_voucher_id').value.trim();
		var eVoucherID = document.getElementById('e_voucher_id').value.trim();
		
		//alert(startNo + ":" + endNo + ":" + query);
		
		if(dbName.length > 0 && sVoucherNo.length > 0 && sVoucherID.length > 0 &&  eVoucherID.length > 0)
		{
			document.getElementById('msg').innerHTML = 'Message Log : <br />Updating DB. Please Wait...';
			$.ajax({
				url : "script/Update_voucher_numbers.script.php",
				type : "POST",
				data: { "dbName": dbName, 
						"sVoucherNo":sVoucherNo,
						"sVoucherID": sVoucherID, 
						"eVoucherID": eVoucherID},
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

</script>
</head>
<body>
	<h3>Select the Database to Update Voucher Number</h3>
	<b>DB Name : </b>hostmjbt_society&nbsp;<input type="text" name="dbname" id="dbname" />
	<br /><br />
    <b>Start Voucher No : </b>&nbsp;<input type="text" name="s_voucherno" id="s_voucherno" />
	<br /><br />
    <b>Start Voucher ID : </b>&nbsp;<input type="text" name="s_voucher_id" id="s_voucher_id" />
	<br /><br />
    <b>End Voucher ID: </b>&nbsp;<input type="text" name="e_voucher_id" id="e_voucher_id" />
	<br /><br />
	<input type="button" value="Submit" onclick="updateTables();" />
	<br /><br />
	<div id="msg" style="color:#F00;">Message Log : <br /></div>
</body>
</html>
function deleteCustRemType(str)
{
	$.ajax
	({
		url: "ajax/CustRemType.ajax.php",
		type: "POST",
		datatype: "JSON",
		data: {"method":"deleteCustremdetails","Id":str},
		success: function(data)
		{
			alert ("Data deleted Successfully");
			var b=data.trim();
			if(b == "1")
			{
				window.location.href = "viewcustomerreminder.php?type=active";
			}
			else
			{
				alert ("Problem in deleting the selected record.");
			}
			
		}
		
	});
}


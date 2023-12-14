// JavaScript Document
function deleteParkingType(str)
{
	//alert (str);
	$.ajax
	({
		url : "ajax/parkingType.ajax.php",
		type : "POST",
		datatype: "JSON",
		data : {"method":"deleteParkingType","Id":str},
		success : function(data)
		{
			alert ("Res:"+data);
			var a=data.trim();
			if(a == "1")
			{
				window.location.href = "viewParkingType.php";
			}
			else
			{
				alert ("Problem in deleting the selected record.")
			}
		}
	});
}
//javasrcipt
function deleteLien(str, unitId)
{
	//alert (unitId);
	$.ajax
	({
		url : "ajax/lien.ajax.php",
		type : "POST",
		datatype: "JSON",
		data : {"method":"deleteLien","lienId":str},
		success : function(data)
		{
			//alert ("Res:"+data);
			var a=data.trim();
			var res = Array();
			res = a.split("@@@");
			//alert (res[1]);
			if(res[1] == "1")
			{
				window.location.href = "lien.php?type=open&unit_id="+unitId;
			}
			else
			{
				alert ("Problem in deleting the selected record.")
			}
		}
	});
}
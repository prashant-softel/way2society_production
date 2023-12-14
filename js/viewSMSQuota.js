// JavaScript Document
function getSMSQuotaDetails()
{
	var societyId=document.getElementById("societyId").value;
	//alert ("SocietyId:"+societyId);
	$.ajax
	({
		url : "ajax/viewSMSQuota.ajax.php",
		type : "POST",
		datatype: "JSON",
		data : {"method":"getSMSQuota","societyId":societyId},
		success : function(data)
		{
			location.reload();
		}
	});
}
function deleteSMSQuota(str)
{
	$.ajax
	({
		url : "ajax/viewSMSQuota.ajax.php",
		type : "POST",
		datatype: "JSON",
		data : {"method":"deleteSMSQuota","SMSQuotaId":str},
		success : function(data)
		{
			var a = data.trim();
			if(a == "1")
			{
				window.location.href = "viewSMSQuota.php";
			}
			else
			{
				alert ("Problem in deleting record.");
				
			}
		}
	});
}
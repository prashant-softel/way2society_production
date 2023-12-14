// JavaScript Document
function deleteComment(commentId,taskId)
{
	//alert (commentId);
	//alert (taskId);
	$.ajax
	({
		url : "ajax/comment.ajax.php",
		type : "POST",
		datatype: "JSON",
		data : {"method":"deleteComment","Id":commentId},
		success : function(data)
		{
			//alert ("Res:"+data);
			var a=data.trim();
			if(a == "1")
			{
				window.location.href = "viewTasks.php?taskId="+taskId;
			}
			else
			{
				alert ("Problem in deleting the selected record.")
			}
		}
	});
}
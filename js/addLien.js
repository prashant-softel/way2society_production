// JavaScript Document
function deleteDocument(str)
{
	//alert ("str:"+str);
	var docId = document.getElementById("docId"+str).value;
	$.ajax
	({
		url : "ajax/addLien.ajax.php",
		type : "POST",
		datatype: "JSON",
		data : {"method":"deleteDocument","docId":docId},
		success : function(data)
		{
			var a		= data.trim();	
			var arr1	= new Array();
			var arr2	= new Array();
			arr1		= a.split("@@@");
			if(arr1[1]==1)
			{
				document.getElementById("docId"+str).style.display="none";
				document.getElementById("docName"+str).style.display="none";
				document.getElementById("fileName"+str).style.display="none";
				document.getElementById("btnDelete"+str).style.display="none";
			}
			//add comment for logic used behind following code..
			$.ajax
			({
				url : "ajax/lien.ajax.php",
				type : "POST",
				datatype: "JSON",
				data : {"method":"editDocumentDetails","unit_id":arr2[2]},
				success : function(data1)
				{
							//alert ("res :"+data1);
					var a		= data1.trim();	
					var arr1	= new Array();
					var arr2	= new Array();
					arr1		= a.split("@@@");
					arr2		= arr1[1].split("#");
					
					if(arr2.length > 1)
					{
						document.getElementById("uploadedDocDetails").style.display="table";
					}
					else
					{
						document.getElementById("uploadedDocDetails").style.display="none";
					}
				}
			});
		}
	});
}
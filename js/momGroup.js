function getgroup(str)
{		
	var iden	= new Array();
	iden		= str.split("-");
	var groupId= iden[1].trim().replace(/ /g, '%20')
	//alert ("In get group:"+str);
	<!--$('html, body').animate({ scrollTop: $('#top').offset().top }, 300);-->
	//alert ("Method:"+iden[0]);
	if(iden[0]=="delete")
	{
		
		var conf = confirm("Are you sure , you want to delete it ?");
		if(conf==1)
		{			
			$.ajax
			({
				url : "ajax/momGroup.ajax.php",
				type : "POST",
				datatype: "JSON",
				data : {"method":"delete","gId":iden[1]},
				success : function(data)
				{	
					//alert ("Data:"+data);
					window.location.href ="momGroup.php";
				}	
			});
		}
	}
	else
	{	
		//document.getElementById("hide").style.display="table";	
		//alert ("In edit");
		//remoteCall("ajax/momGroup.ajax.php","form=group&method="+iden[0]+"&groupId="+iden[1],"loadchanges");
		window.location.href="createGrp.php?method="+iden[0]+"&groupId="+iden[1];
	}
}

function loadchanges()
{
	//alert (sResponse);
  	var a    = sResponse.trim();
  	//alert ("a:"+a); 
	var arr1	= new Array();
	var arr2	= new Array();	
	arr1		= a.split("@@@");
	//alert ("arr1: "+arr1[1]);
	arr2		= arr1[1].split("#");
	//alert ("arr2 of 1 : "+arr2[1]);
	//alert ("arr2 of 2 : "+arr2[2]);
	if(arr1[0] == "edit")
	{		
		document.getElementById('groupname').value=arr2[1];
		document.getElementById('groupdes').value=arr2[2];
		document.getElementById("id").value=arr2[0];
		document.getElementById("insert").value="Update";
	}
}
  
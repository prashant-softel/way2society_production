// JavaScript Document

function getService(str)
{ 		
	var iden=new Array();
	iden=str.split("-");		
	//alert(iden);
	if(iden[0]=="delete")
	{
		var conf = confirm("Are you sure , you want to delete it ???");
		if(conf==true)
		{			
			remoteCall("ajax/ajaxhelpline.php","&method="+iden[0]+"&requestId="+iden[1],"loadchanges");
			
		}
	}
}


function loadchanges()
{	
		
	var a		= sResponse.trim();			
	var arr1	= new Array();
	var arr2	= new Array();
	
	arr1		= a.split("@@@");
	arr2		= arr1[1].split("^");		

	if(arr1[0] == "delete")
	{		
		//window.location.href ="../notices.php?mst&"+arr1[1]+"&mm";
		alert("Record Deleted Successfully");		
		window.location.href ="helpline.php";
	}
}
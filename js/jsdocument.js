function getdocument(str)
{
	var iden=new Array();
	iden=str.split("-");

	if(iden[0]=="delete")
	{
		var d=confirm("Are you sure , you want to delete it ???");
		if(d==1)
		{
			remoteCall("ajax/ajaxdocument.php","form=document&method="+iden[0]+"&documentId="+iden[1],"loadchanges");
		}
	}
	else
	{
			remoteCall("ajax/ajaxdocument.php","form=document&method="+iden[0]+"&documentId="+iden[1],"loadchanges");
	}
}

function loadchanges()
{
	var a=trim(sResponse);
	var arr1=new Array();
	var arr2=new Array();
	arr1=a.split("@@@");
	arr2=arr1[1].split("#");
	if(arr1[0] == "edit")
	{
		document.getElementById('document1').value=arr2[1];
		document.getElementById("id").value=arr2[0];
		document.getElementById("insert").value="Update";
	}
	else if(arr1[0] == "delete")
	{
		window.location.href ="../document.php?mst&"+arr1[1]+'&mm';
	}
	function LTrim( value )
	{
	var re = /\s*((\S+\s*)*)/;
	return value.replace(re, "$1");
	}
	function RTrim( value )
	{
	var re = /((\s*\S+)*)\s*/;
	return value.replace(re, "$1");
	}
	function trim( value )
	{
	return LTrim(RTrim(value));
	}
}

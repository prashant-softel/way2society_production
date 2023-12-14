function val()
{

	var name=trim(document.getElementById('name').value);
	var contact=trim(document.getElementById('number').value);	
	var email=trim(document.getElementById('email').value);
	var societyname=trim(document.getElementById('soc_name').value);
	var desg=trim(document.getElementById('desg').value);
	var no_of_unit=trim(document.getElementById('no_of_unit').value);
	//cvar societyadd=trim(document.getElementById('soc_add').value);
	var societyadd=document.getElementById('soc_add').value;
	if(societyname=="")
	{	
		document.getElementById('error').innerHTML = "Please provide society name";
		alert(document.getElementById('error').innerHTML);
		document.getElementById('soc_name').focus();
		go_error();
		return false;
	}
	if(name=="")
	{	
		document.getElementById('error').innerHTML = "Please provide name";
		alert(document.getElementById('error').innerHTML);
		document.getElementById('name').focus();
		go_error();
		return false;
	}
	if(contact=="")
	{	
		document.getElementById('error').innerHTML = "Please provide contact number";
		alert(document.getElementById('error').innerHTML);
		document.getElementById('number').focus();
		go_error();
		return false;
	}
	if(email=="")
	{	
		document.getElementById('error').innerHTML = "Please provide email address";
		alert(document.getElementById('error').innerHTML);
		document.getElementById('email').focus();
		go_error();
		return false;
	}		
	if(desg=="")
	{	
		document.getElementById('error').innerHTML = "Please provide designation";
		alert(document.getElementById('error').innerHTML);
		document.getElementById('desg').focus();
		go_error();
		return false;
	}	
	if(no_of_unit=="")
	{	
		document.getElementById('error').innerHTML = "Please provide no of units in this society";
		alert(document.getElementById('error').innerHTML);
		document.getElementById('no_of_unit').focus();
		go_error();
		return false;
	}
	if(societyadd=="")
	{	
		document.getElementById('error').innerHTML = "Please provide society address";
		alert(document.getElementById('error').innerHTML);
		document.getElementById('soc_add').focus();
		go_error();
		return false;
	}		
//return false;
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
// function getself_sign(str)
// {
// 	var iden	= new Array();
// 	iden		= str.split("-");

// 	$('html, body').animate({ scrollTop: $('#top').offset().top }, 300);

// 	if(iden[0]=="delete")
// 	{
// 		var conf = confirm("Are you sure , you want to delete it ???");
// 		if(conf==1)
// 		{
// 			$(document).ready(function()
// 			{
// 				$("#error").fadeIn("slow");
// 				document.getElementById("error").innerHTML = "Please Wait...";
// 			});

// 			remoteCall("../ajax/self_sign.ajax.php","form=self_sign&method="+iden[0]+"&self_signId="+iden[1],"loadchanges");
// 		}
// 	}
// 	else
// 	{
// 		$(document).ready(function()
// 		{
// 			$("#error").fadeIn("slow");
// 			document.getElementById("error").innerHTML = "Please Wait...";
// 		});

// 		remoteCall("../ajax/self_sign.ajax.php","form=self_sign&method="+iden[0]+"&self_signId="+iden[1],"loadchanges");
// 	}
// }

// function loadchanges()
// {
// 	var a		= sResponse.trim();
// 	var arr1	= new Array();
// 	var arr2	= new Array();
// 	arr1		= a.split("@@@");
// 	arr2		= arr1[1].split("#");


// 	if(arr1[0] == "edit")
// 	{
// 		$(document).ready(function()
// 		{
// 			$("#error").fadeIn("slow");
// 			document.getElementById("error").innerHTML = "Please Wait...";
// 		});

// 		document.getElementById('name').value=arr2[1];
// 		document.getElementById('number').value=arr2[2];
// 		document.getElementById('email').value=arr2[3];
// 		document.getElementById('soc_name').value=arr2[4];
// 		document.getElementById('no_of_unit').value=arr2[5];
// 		document.getElementById("id").value=arr2[0];
// 		document.getElementById("insert").value="Update";
// 	}
// 	else if(arr1[0] == "delete")
// 	{
// 		$(document).ready(function()
// 		{
// 			$("#error").fadeIn("slow");
// 			document.getElementById("error").innerHTML = "Please Wait...";
// 		});

// 		window.location.href ="../self_sign.php?mst&"+arr1[1]+"&mm";
// 	}
// }

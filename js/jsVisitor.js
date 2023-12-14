// JavaScript Document
function val()
{
	///////////////////////////////////////////////////////////////////////////	
	var expVisitorNumber = trim(document.getElementById('contactNo').value);	
	var firstName = trim(document.getElementById('firstName').value);
	var LastName = trim(document.getElementById('LastName').value);	
	var ExpDate = trim(document.getElementById('ExpDate').value);	
	var purpose = trim(document.getElementById('purpose').value);	

	///////////////////////////////////////////////////////////////////////////	
	if(expVisitorNumber=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter visitor mobile number";
		document.getElementById("contactNo").focus();
		
		go_error();
		return false;
	}
	if(firstName=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter visitor first name";
		document.getElementById("firstName").focus();
		
		go_error();
		return false;
	}
	if(LastName=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter visitor last name";
		document.getElementById("LastName").focus();
		
		go_error();
		return false;
	}
	
	if(ExpDate=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please select expected date";
		document.getElementById("ExpDate").focus();
		
		go_error();
		return false;
	}
	
	if(purpose == "")
	{
		document.getElementById('error').style.display = '';
		document.getElementById("error").innerHTML = "Please select purpose";
		document.getElementById('purpose').focus();
		
		go_error();
		return false;
	}
	
	
	///////////////////////////////////////////////////////////////////////////	
	
	
	///////////////////////////////////////////////////////////////////////////	
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
	//return true;
	///////////////////////////////////////////////////////////////////////////	
}

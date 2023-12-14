function val()
{
	var status = document.getElementById('status').value;
	var comments = document.getElementById('comments').value;
	var priority = trim(document.getElementById('priority').value);
	if(status == 0)
	{
		document.getElementById('error').style.display = "";
		document.getElementById('error').innerHTML = "Please Select Status";
		document.getElementById('status').focus();
		go_error();
		return false;	
	}
	
	if(comments == "")
	{
		document.getElementById('error').style.display = "";
		document.getElementById('error').innerHTML = "Please Enter Comments";
		document.getElementById('comments').focus();
		go_error();
		return false;
	}
	if(priority == 0)
	{
		document.getElementById('error').style.display = "";
		document.getElementById('error').innerHTML = "Please Select Priority";
		document.getElementById('priority').focus();
		go_error();
		return false;
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
function getService(str)
{ //alert("hi");	
	var iden=new Array();
	iden=str.split("-");		
	//alert(iden);
	if(iden[0]=="delete")
	{
		var conf = confirm("Are you sure , you want to delete it ???");
		if(conf==1)
		{			
			remoteCall("ajax/ajaxservicerequest.php","&method="+iden[0]+"&requestId="+iden[1],"loadchanges");
		}
	}
	else
	{
		remoteCall("ajax/ajaxservicerequest.php","&method="+iden[0]+"&requestId="+iden[1],"loadchanges");			
	}
}

function loadchanges()
{	
	var a		= sResponse.trim();			
	var arr1	= new Array();
	var arr2	= new Array();
	arr1		= a.split("@@@");
	arr2 = 		JSON.parse("["+arr1[1]+"]");
	arr2 =      arr2[0][0];
	//arr2		= arr1[1].split("^");		
	//alert(arr2);		
	if(arr1[0] == "edit")
	{		
		document.getElementById("request_id").value=arr2.request_id;
		document.getElementById('reportedby').value=arr2.reportedby;
		//document.getElementById('complaint_date').value=arr2[2];
		document.getElementById('email').value=arr2.email;
		document.getElementById('phone').value=arr2.phone;
		document.getElementById('priority').value=arr2.priority;
		document.getElementById('category').value=arr2.category;
		//document.getElementById('desp').value=arr2[9];
		document.getElementById('summery').innerHTML=arr2.summery;
		CKEDITOR.instances['details'].setData(arr2.details);
		document.getElementById('request_no').value = arr2.request_no;
		document.getElementById("insert").value = "Update";	
    console.log('unit',arr2.unit_id);
    console.log(document.getElementById("unit_no2"));
		document.getElementById("unit_no2").value=arr2.unit_id;
    console.log(document.getElementById("unit_no2").value);
		//CKEDITOR.instances.description.setData(arr2[5]);																				
	}
	else if(arr1[0] == "delete")
	{		
		//window.location.href ="../notices.php?mst&"+arr1[1]+"&mm";		
		window.location.href ="servicerequest.php";
	}
}

function blockNonNumbers(obj, e, allowDecimal, allowNegative)
{
	var key;
	var isCtrl = false;
	var keychar;
	var reg;
		
	if(window.event) {
		key = e.keyCode;
		isCtrl = window.event.ctrlKey
	}
	else if(e.which) {
		key = e.which;
		isCtrl = e.ctrlKey;
	}
	
	if (isNaN(key)) return true;
	
	keychar = String.fromCharCode(key);
	
	// check for backspace or delete, or if Ctrl was pressed
	if (key == 8 || isCtrl)
	{
		return true;
	}

	reg = /\d/;
	var isFirstN = allowNegative ? keychar == '-' && obj.value.indexOf('-') == -1 : false;
	var isFirstD = allowDecimal ? keychar == '.' && obj.value.indexOf('.') == -1 : false;
	
	return isFirstN || isFirstD || reg.test(keychar);
}
function del_photo(img,qr)
	{
		
		//alert(qr);
		//alert(img);
		if(qr!="")
		{
			var con = confirm("Are you sure,You want to delete the images ?");
			
			if(con==true)
			{	
				//document.getElementById('error_home_page').style.display = '';	
				document.getElementById("error").innerHTML = "Please Wait...";
				
				remoteCall("ajax/ajaxservicerequest.php","form=servicerequest&method=del_photo&qr="+qr+"&img="+img,"deletedImage");	
			}
		}	
	}
function deletedImage(img,qr)
{
	//alert("image deleted");
	//window.reload();
	location.reload();
}
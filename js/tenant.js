var bTerminate = false;

function val()
{
	//alert("test");
var name=trim(document.getElementById('t_name').value);
var mobile=trim(document.getElementById('contact_1').value);
var email =trim(document.getElementById('email_1').value);
//var member=trim(document.getElementById('member').value);
var startDate=trim(document.getElementById('start_date').value);
var endDate=trim(document.getElementById('end_date').value);
//var upload=trim(document.getElementById('upload').value);
var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
var submit_type = document.getElementById('insert').value;

var CreateLogin = document.getElementById("chkCreateLogin").checked;
var send_commu_emails = document.getElementById("other_send_commu_emails").checked;

if(name=="")
	{	
		document.getElementById('error').innerHTML = "Please Provid Name";
		alert(document.getElementById('error').innerHTML);
		document.getElementById('t_name').focus();
		go_error();
		return false;
	}
	if(startDate=="")
	{	
		document.getElementById('error').innerHTML = "Please Provid Lease Start Date";
		alert(document.getElementById('error').innerHTML);
		document.getElementById('start_date').focus();
		go_error();
		return false;
	}		
		if(endDate=="")
	{	
		document.getElementById('error').innerHTML = "Please Provid Lease End Date";
		alert(document.getElementById('error').innerHTML);
		document.getElementById('start_date').focus();
		go_error();
		return false;
	}
		
	/*if(mobile=="")
	{	
		document.getElementById('error').innerHTML = "Please Enter Mobile Number ";
		alert(document.getElementById('error').innerHTML);
		document.getElementById('contact_1').focus();
		go_error();
		return false;
	}		
	if(email=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please Enter Email Address";
		document.getElementById('email_1').focus();
		
		go_error();
		return false;
	}
	else
	{
		if(reg.test(email) == false) 
		{
			document.getElementById('error').style.display = '';
			document.getElementById('error').innerHTML = 'Invalid Email Address';	
			document.getElementById('email_1').focus();
			
			go_error();
			return false;	
		}		
	}*/
	if(CreateLogin  == true || send_commu_emails  == true)
	{
		if(document.getElementById('other_email').value == "")
		{
			alert("Please provide Email id");
			return false; 
		}
		else
		{
			var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;  
			if(document.getElementById("other_email").value.match(mailformat))  
			{  
				//alert("Email address valid!");  
				return true;  
			}  
			else  
			{  
				alert("Entered email address is not in valid email format !");  
				document.getElementById("other_email").focus();  
				return false;  
			}
		}
	}
	/*if(member=="")
	{	
		document.getElementById('error').innerHTML = "Please Provid Additional Member Name";
		alert(document.getElementById('error').innerHTML);
		document.getElementById('member').focus();
		go_error();
		return false;
	}*/		
	
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

function del_Doc(id,Tid)
	{
		//alert	(doc +':' + id);
		//alert("id"+id);
		//alert("Tid"+Tid);
		if(id!="")
		{
			var con = confirm("Are you sure,You want to delete the images ?");
			
			if(con==true)
			{	
				//document.getElementById('error_home_page').style.display = '';	
				document.getElementById("error").innerHTML = "Please Wait...";
				
				remoteCall("ajax/tenant.ajax.php","form=tenant&method=del_Doc&id="+id+"&Tid="+Tid,"deleteDocument");	
			}
		}	
	}
function deleteDocument(id,Tid)
{
	//alert("image deleted");
	//window.reload();
	location.reload();
}


///////////  delete Documents File///////////////////////////////////////////
//
//function del_document(doc,qr)
//	{
//		
//		//alert(qr);
//		//alert(doc);
//		if(qr!="")
//		{
//			var con = confirm("Are you sure,You want to delete the images ?");
//			
//			if(con==true)
//			{	
//				//document.getElementById('error_home_page').style.display = '';	
//				document.getElementById("error").innerHTML = "Please Wait...";
//				
//				remoteCall("ajax/tenant.ajax.php","form=tenant&method=del_Doc&qr="+qr+"&doc="+doc,"deletedImage");	
//			}
//		}	
//	}
function deletedImage(doc,qr)
{
	//alert("image deleted");
	//window.reload();
	location.reload();
}


///////////////////////////////////////////////////// fetch  tenant details///////////////////////////////////////////////////////////

function  FetchTenantHistory(society_id)
{
	//alert("test");
	showLoader();
	var TenantList = document.getElementById('TenantList').value;
	
	$.ajax({
			url : "ajax/tenant.ajax.php",
			type : "POST",
			data : {"method" : 'fetch',"societyID" : society_id,"TenantList" : TenantList},
			beforeSend: function()
			{
				document.getElementById('showTable').innerHTML = '<center><font color="blue">Fetching Records Please Wait...</font></center>';
			},
			success : function(data)
			{	
				document.getElementById('showTable').innerHTML = data;
				hideLoader();
				
				if(document.getElementById('AllowExport').value  ==  1 && $.trim(data))
				{
					//data not empty
					document.getElementById('btnExport').style.display = 'block';
					document.getElementById('Print').style.display = 'block';
				}
				else
				{
					//data empty
					document.getElementById('btnExport').style.display = 'none';
					document.getElementById('Print').style.display = 'none';
				}
				
			},
				
			fail: function()
			{
				
			},
			
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
			}
		});
		
}

function PrintPage() 
{
	var originalContents = document.body.innerHTML;
	document.getElementById('societyname').style.display ='block';	
	var printContents = document.getElementById('showTable').innerHTML;
	
	document.body.innerHTML = printContents;
	window.print();

	document.body.innerHTML= originalContents;
}

//////////////////////////////////////////////////////////////////////////  user tenant list /////////////////////////////////////////////////////////////////////////////

function  FetchTenantList(society_id,unit_id)
{
	//alert("test");
	showLoader();
	var TenantList = document.getElementById('TenantList').value;
	
	$.ajax({
			url : "ajax/tenant.ajax.php",
			type : "POST",
			data : {"method" : 'fetchList',"societyID" : society_id, "unitID" : unit_id, "TenantList" : TenantList},
			beforeSend: function()
			{
				document.getElementById('showTable').innerHTML = '<center><font color="blue">Fetching Records Please Wait...</font></center>';
			},
			success : function(data)
			{	
				document.getElementById('showTable').innerHTML = data;
				hideLoader();
				
				if(document.getElementById('AllowExport').value  ==  1 && $.trim(data))
				{
					//data not empty
					document.getElementById('btnExport').style.display = 'block';
					document.getElementById('Print').style.display = 'block';
				}
				else
				{
					//data empty
					document.getElementById('btnExport').style.display = 'none';
					document.getElementById('Print').style.display = 'none';
				}
				
			},
				
			fail: function()
			{
				
			},
			
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
			}
		});
		
}

function PrintPage() 
{
	var originalContents = document.body.innerHTML;
	document.getElementById('societyname').style.display ='block';	
	var printContents = document.getElementById('showTable').innerHTML;
	
	document.body.innerHTML = printContents;
	window.print();

	document.body.innerHTML= originalContents;
}

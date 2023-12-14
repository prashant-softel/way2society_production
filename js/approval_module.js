function val()
{
	//alert("Test");
	var post_date = trim(document.getElementById('post_date').value);	
	var approval_date = trim(document.getElementById('exp_date').value);
	var subject = trim(document.getElementById('subject').value);	
	var members_selected = trim(document.getElementById('approval').value);	
	var approval_requird = trim(document.getElementById('min').value);	
	if(post_date=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please select post date";
		document.getElementById("post_date").focus();
		go_error();
		return false;
	}
	if(approval_date=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please select approval date";
		document.getElementById("exp_date").focus();
		go_error();
		return false;
	}
	if(subject=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter module subject";
		document.getElementById("subject").focus();
		go_error();
		return false;
	}
	if(members_selected=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please select get approval committee members";
		document.getElementById("post_date").focus();
		go_error();
		return false;
	}
	if(approval_requird=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter minmum approval required";
		document.getElementById("min").focus();
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

function getapproval_module(str)
{
	var iden	= new Array();
	iden		= str.split("-");
	if(iden[0]=="delete")
	{
		var conf = confirm("Are you sure , you want to delete it ???");
		if(conf==1)
		{
			
			remoteCall("ajax/approval_module.ajax.php","form=approval_module&method="+iden[0]+"&approval_moduleId="+iden[1],"loadchanges");
		}
	}
	else
	{
		

		remoteCall("ajax/approval_module.ajax.php","form=approval_module&method="+iden[0]+"&approval_moduleId="+iden[1],"loadchanges");
	}
}
function loadchanges()
{
	//alert("Call LOad Function");
	var a		= sResponse.trim();
	var arr1	= new Array();
	var arr2	= new Array();
	arr1		= a.split("@@@");
	//arr2		= arr1[1].split("#");
	//alert(arr1);
	if(arr1[0] == "edit")
	{
		//alert(arr2[5]);
		var arResponse	=  JSON.parse(arr1[1].trim());	
		
		document.getElementById('id').value=arResponse["id"];
		document.getElementById('issueby').value=arResponse["issued_by"];
		document.getElementById('post_date').value=arResponse["post_date"];
		document.getElementById('exp_date').value=arResponse["end_date"];
		document.getElementById('subject').value=arResponse["subject"];
		document.getElementById("notify").checked= (arResponse["notify_email"] == 1) ? true : false;
		document.getElementById("sms_notify").checked= (arResponse["sms_notify"] == 1) ? true : false;
		document.getElementById("total").value=arResponse["Approvals_selected_count"];
		document.getElementById("min").value=arResponse["Approval_required_count"];
		document.getElementById("description").value=arResponse["description"];
		/*-------------------------- Selected Members -------------------*/
		var members = Array();
		members =arResponse["mem_id"].split(",");
		var s = document.getElementById("approval");			
		s.options[0].selected = false;
		for ( var k = 0; k < s.options.length; k++ )
		{																												
			if(members.indexOf(s.options[k].value) >= 0)
			{																												
				s.options[k].selected = true;																							
			}										
		}
		
		
		
		/*var Document = Array();
		Document = arResponse["documents"].split(",");	
		console.log(Document);
		doc_data = Document.split("#");	
		console.log(doc_data);*/		
		/*var s = document.getElementById("post_noticeto");			
		s.options[0].selected = false;
		for ( var k = 0; k < s.options.length; k++ )
		{																												
			if(units.indexOf(s.options[k].value) >= 0)
			{																												
				s.options[k].selected = true;																							
			}										
		}*/
		document.getElementById("insert").value="Update";
	}
	/*document.getElementById("notify").checked = (arResponse["isNotify"] == 1)? true : false;
		var IsCheckedSMS = document.getElementById("sms_notify").checked = (arResponse["sms_notify"] == 1)? true : false;
		if(IsCheckedSMS == true)
		{
			SetSMS();
		}*/
	else if(arr1[0] == "delete")
	{
		
		//alert("Data Deleted");
		
			window.location.href ="approvals.php?type=active";
	
		//window.location.href ="../approval_module.php?mst&"+arr1[1]+"&mm";
	}
}



/*function showTestSMS()
	{	
		var userMobileNo = document.getElementById('userMobileNo').value;
		var TestMobileNo = prompt("Please Enter the Mobile Number",userMobileNo);
		var SMSTemplate = document.getElementById('SMSTemplate').value;
		$.ajax
			({
				url : "ajax/ajaxnotice.ajax.php",
				type : "POST",
				data : {"method":"SMSTest", "TestMobileNo":TestMobileNo, "SMSTemplate":SMSTemplate},
				success : function(data)
				{	
					NewData = (data.trim());
					var response = NewData.split(",");
					if(response[1] == "success")
					{
						alert("Message sent successfully!!");
					}
				}
			});
		
		
	}
	function SetSMS()
	{	
		var OriginalSub = '';
		var IsSubChange = 1;
		var IsChecked = document.getElementById('sms_notify').checked;
		var NoticeSubject = document.getElementById('subject').value;
		var IsUpdateRequest = document.getElementById('updaterowid').value;
	
			if(IsUpdateRequest != 0)
			{
				OriginalSub = document.getElementById('NoticeSubject').value;
				console.log(NoticeSubject+ '='+ OriginalSub)
				if(OriginalSub == NoticeSubject)
				{		
					IsSubChange = 0;
				}
			}
	
		
		if(IsChecked == true)
		{
			if(NoticeSubject == '')
			{
				alert("Please First Mention the Subject for Notice.");
				document.getElementById('sms_notify').checked = false;
				return false;
			}
			$.ajax
			({
				
				url : "ajax/ajaxnotice.ajax.php",
				type : "POST",
				data : {"method":"ShowSMSTemplate", "subject":NoticeSubject, "IsUpdateRequest":IsUpdateRequest, "IsSubChange":IsSubChange, "OriginalSub":OriginalSub},
				
				success : function(data)
				{	
					NewData = (data.trim());
					document.getElementById('smsTest').style.display = "block";
					document.getElementById('SMSTemplate').value = NewData;
				}
			});
		}
		else
		{
			document.getElementById('smsTest').style.display = "none";
		}
		
	}*/
	
	/*function TotalApprovals()
	{
		var select = document.getElementById('approval').selected=true;
		console.log(select);
		select.onclick = function () 
		{
    	var opts = select.options;
    	var len = 0;
   		for (i = 0; i < opts.length; i++) {
        if (opts[i].selected && opts[i].value) len++;
   	    }
    document.getElementById('hidden').value = len * (parseInt(document.getElementById('total').value || 0))
};
		
	}*/



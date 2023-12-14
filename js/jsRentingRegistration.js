// JavaScript Document
function saveOwnersDetails()
{
	var memberId = document.getElementById("memberId").value;
	//alert ("memberId "+memberId);
	var dob = document.getElementById("dob").value;
	//alert ("dob:"+dob);
	var gender = document.getElementById("Gender").value;
	var unitId = document.getElementById("unitId").value;
	var view = document.getElementById("view").value;
	//alert ("gender:"+gender);
	$.ajax
	({
		url : "ajax/rentingRegistration.ajax.php",
		type : "POST",
		datatype: "JSON",
		data : {"method":"updateOwnerDetails","memberId":memberId,"gender":gender,"dob":dob},
		success : function(data)
		{
			//alert (data);
			var ownerDetails=Array();
			ownerDetails=JSON.parse(data);
			var updateResult = String(ownerDetails['success']);
			if(updateResult == "1")
			{
				window.location.href="RentingRegistration.php?type=tenant&unitId="+unitId+"&View="+view;	
			}
			else
			{
				alert("Unable to connect. Please try again.")
			}
		}
	});
}
function saveTenantsDetails()
{
	var method = document.getElementById("btnSubmitTenant").value;
	if(method == "Save")
	{
		var unitId = document.getElementById("unitId").value;
		var view = document.getElementById("view").value;
		var fName = document.getElementById('TFName').value;
		fName = fName.trim();
		var mName = document.getElementById('TMName').value;
		mName = mName.trim();
		var lName = document.getElementById('TLName').value;
		lName = lName.trim();
		var mContactNo = document.getElementById('contact_1').value;
		mContactNo = mContactNo.trim();
		var mEmaild = document.getElementById('email_1').value;
		mEmaild = mEmaild.trim();
		var memCount = document.getElementById("tenantCount").value;
		memCount = memCount.trim();
	//alert ("tenantCount"+memCount)
		var login = document.getElementById('chkCreateLogin').value;
		var sendEmail = document.getElementById('other_send_commu_emails').value;
		var memberName=[];
		var relation=[];
		var dob=[];
		var contactNo=[];
		var emailAdd=[];
		var i=1;
		var tenantModuleId = 0;
		if(memCount > 1)
		{
			for(i=1;i<=memCount;i++)
			{
				memberName[i-1] = document.getElementById('members_'+i).value;
				relation[i-1] = document.getElementById('relation_'+i).value;
				dob[i-1] = document.getElementById('mem_dob_'+i).value;
				contactNo[i-1] = document.getElementById('contact_'+i).value;
				emailAdd[i-1] = document.getElementById('email_'+i).value;
			}
		}
		var profession = document.getElementById("TProfession").value;
		profession = profession.trim();
		var add1 = document.getElementById("TAddress1").value;
		add1=add1.trim().replace(/ /g,'%20');
		var add2 = document.getElementById("TAddress2").value;
		add2=add2.trim().replace(/ /g,'%20');
		var city = document.getElementById("TCity").value;
		city = city.trim();
		var pincode = document.getElementById("TPincode").value;
		pincode = pincode.trim();
		var tenantType = document.getElementById("TCompany").checked;
		var cardNo = "";
	//alert (value);
		if(tenantType)
		{
			cardNo = document.getElementById("TCINno").value;
		}
		else
		{
			cardNo = document.getElementById("TAdhaarNo").value;
		}
		cardNo = cardNo.trim();
		var view = document.getElementById("view").value;
		var TenantModuledob = document.getElementById("mem_dob_1").value;
	//alert ("gender:"+gender);
	//alert ("Tenant Module dob :"+TenantModuledob)
		$.ajax
		({
			url : "ajax/rentingRegistration.ajax.php",
			type : "POST",
			datatype: "JSON",
			data : {"method":"addNewTenant","unitId":unitId,"fName":fName,"mName":mName,"lName":lName,"memCount":memCount,"login":login,"sendEmail":		sendEmail,"profession":profession,"dob":TenantModuledob,"add1":add1,"add2":add2,"city":city,"pincode":pincode,"cardNo":cardNo,"mContactNo":mContactNo,"mEmaild":mEmaild},
			success : function(data)
			{
			//alert ("data:"+data);
				var tenantDetails=Array();
				tenantDetails=JSON.parse(data);
			//alert (tenantDetails['response']['TenantId']);
				var updateResult = String(tenantDetails['success']);
				if(updateResult == "1")
				{
					tenantModuleId = parseInt(tenantDetails['response']['TenantId']);
					alert ("tenantModuleId :"+tenantModuleId);
					document.getElementById("tenantId").value = tenantDetails['response']['TenantId'];
					//insertTenantMember(tenantModuleId);
					if(tenantModuleId > 0)
					{
						$.ajax
						({
							url : "ajax/rentingRegistration.ajax.php",
							type : "POST",
							datatype: "JSON",
							data : {"method":"addNewTenantMembers","tenantModuleId":tenantModuleId,"memCount":memCount,"memberName":JSON.stringify(memberName),"relation":JSON.stringify(relation),"dob":JSON.stringify(dob),"contactNo":JSON.stringify(contactNo),"emailAdd":JSON.stringify(emailAdd)},
					  	  	success : function(data1)
							{
							//alert ("data1 :"+data1);
								if(data1 == "success")
								{
									window.location.href="RentingRegistration.php?type=agreementTerms&unitId="+unitId+"&View="+view+"&tId="+tenantModuleId;	
								}
								else
								{
									alert("Unable to connect. Please try again.")
								}
							}
						});
					}
				}
				else
				{
					alert("Unable to connect. Please try again.")
				}
			}
		});
	}
	else
	{
		var tenantModuleId = document.getElementById("tenantId").value;
		//alert ("tenantModuleId :"+tenantModuleId);
		var unitId = document.getElementById("unitId").value;
		var view = document.getElementById("view").value;
		var fName = document.getElementById('TFName').value;
		fName = fName.trim();
		var mName = document.getElementById('TMName').value;
		mName = mName.trim();
		var lName = document.getElementById('TLName').value;
		lName = lName.trim();
		var mContactNo = document.getElementById('contact_1').value;
		mContactNo = mContactNo.trim();
		var mEmaild = document.getElementById('email_1').value;
		mEmaild = mEmaild.trim();
		var memCount = document.getElementById("tenantCount").value;
		memCount = memCount.trim();
	//alert ("tenantCount"+memCount)
		var login = document.getElementById('chkCreateLogin').value;
		var sendEmail = document.getElementById('other_send_commu_emails').value;
		var memberId = [];
		var memberName=[];
		var relation=[];
		var dob=[];
		var contactNo=[];
		var emailAdd=[];
		var i=1;
		memberId[0] = 0;
		if(memCount > 1)
		{
			for(i=1;i<=memCount;i++)
			{
				if(i > 1)
				{
					memberId[i-1] = document.getElementById('member_id_'+i).value;	
				}
				memberName[i-1] = document.getElementById('members_'+i).value;
				relation[i-1] = document.getElementById('relation_'+i).value;
				dob[i-1] = document.getElementById('mem_dob_'+i).value;
				contactNo[i-1] = document.getElementById('contact_'+i).value;
				emailAdd[i-1] = document.getElementById('email_'+i).value;
			}
		}
		var profession = document.getElementById("TProfession").value;
		profession = profession.trim();
		var add1 = document.getElementById("TAddress1").value;
		add1=add1.trim().replace(/ /g,'%20');
		var add2 = document.getElementById("TAddress2").value;
		add2=add2.trim().replace(/ /g,'%20');
		var city = document.getElementById("TCity").value;
		city = city.trim();
		var pincode = document.getElementById("TPincode").value;
		pincode = pincode.trim();
		var tenantType = document.getElementById("TCompany").checked;
		var cardNo = "";
	//alert (value);
		if(tenantType)
		{
			cardNo = document.getElementById("TCINno").value;
		}
		else
		{
			cardNo = document.getElementById("TAdhaarNo").value;
		}
		cardNo = cardNo.trim();
		var view = document.getElementById("view").value;
		var TenantModuledob = document.getElementById("mem_dob_1").value;
	//alert ("gender:"+gender);
		//alert ("Tenant Module dob :"+TenantModuledob)
		$.ajax
		({
			url : "ajax/rentingRegistration.ajax.php",
			type : "POST",
			datatype: "JSON",
			data : {"method":"editTenant","tenantModuleId":tenantModuleId,"unitId":unitId,"fName":fName,"mName":mName,"lName":lName,"memCount":memCount,"login":login,"sendEmail": sendEmail,"profession":profession,"dob":TenantModuledob,"add1":add1,"add2":add2,"city":city,"pincode":pincode,"cardNo":cardNo,"mContactNo":mContactNo,"mEmaild":mEmaild},
			success : function(data)
			{
				alert ("data:"+data);
				var tenantDetails=Array();
				tenantDetails=JSON.parse(data);
				//alert (tenantDetails['response']['TenantId']);
				var updateResult = String(tenantDetails['success']);
				if(updateResult == "1")
				{
						$.ajax
						({
							url : "ajax/rentingRegistration.ajax.php",
							type : "POST",
							datatype: "JSON",
							data : {"method":"editTenantMembers","tenantMemId":JSON.stringify(memberId),"memCount":memCount,"memberName":JSON.stringify(memberName),"relation":JSON.stringify(relation),"dob":JSON.stringify(dob),"contactNo":JSON.stringify(contactNo),"emailAdd":JSON.stringify(emailAdd)},
					  	  	success : function(data1)
							{
							//alert ("data1 :"+data1);
								if(data1 == "success")
								{
									window.location.href="RentingRegistration.php?type=agreementTerms&unitId="+unitId+"&View="+view+"&tId="+tenantModuleId+"&action=edt";	
								}
								else
								{
									alert("Unable to connect. Please try again.")
								}
							}
						});
				}
				else
				{
					alert("Unable to connect. Please try again.")
				}
			}
		});
	}//alert ("tenantModuleId :"+tenantModuleId);
}
function getFirstMemberName()
{
	var fName = document.getElementById('TFName').value;
	var mName = document.getElementById('TMName').value;
	var lName = document.getElementById('TLName').value;
	document.getElementById('members_1').value=fName+" "+mName+" "+lName;
}
function validateForm()
{
	//alert("test");
	var result = true;
	var fName=trim(document.getElementById('TFName').value);
	var lName=trim(document.getElementById('TLName').value);
	var mobile=trim(document.getElementById('contact_1').value);
	var email =trim(document.getElementById('email_1').value);

//var startDate=trim(document.getElementById('start_date').value);
//var endDate=trim(document.getElementById('end_date').value);

	var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;

	var CreateLogin = document.getElementById("chkCreateLogin").checked;	
	var send_commu_emails = document.getElementById("other_send_commu_emails").checked;

	if(fName=="")
	{	
		alert("Please Provide Tenant's First Name");
		document.getElementById('TFName').focus();
		result = false;
	}
	if(lName=="")
	{	
		alert("Please Provide Tenant's Last Name");
		document.getElementById('TLName').focus();
		result = false;
	}
	/*if(startDate=="")
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
	}*/
		
	if(mobile=="")
	{	
		alert("Please Enter Mobile Number");
		document.getElementById('contact_1').focus();
		result = false;
	}		
	if(email=="")
	{	
		alert("Please Enter Email Address");
		document.getElementById('email_1').focus();
		result = false;
	}
	else
	{
		if(reg.test(email) == false) 
		{
			alert("Invalid Email Address");	
			document.getElementById('email_1').focus();
			result = false;
		}		
	}
	if(CreateLogin  == true || send_commu_emails  == true)
	{
		/*if(document.getElementById('other_email').value == "")
		{
			alert("Please provide Email id");
			result = false;
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
				result = false;
			}
		}*/
	}
	if(result)
	{
		saveTenantsDetails();
	}
}
function getAdhaarCINno()
{
	var value = document.getElementById("TCompany").checked;
	//alert (value);
	if(value)
	{
		document.getElementById("TAdhaar").style.display = "none";
		document.getElementById("TCIN").style.display = "table-row";
	}
	else
	{
		document.getElementById("TCIN").style.display = "none";
		document.getElementById("TAdhaar").style.display = "table-row";
	}
}
function getNextValue(pValue,pId)
{
	//alert (pValue);
	//alert (pId);
	if(pValue > 11)
	{
		alert ("You can enter number between 1 to 11!");
		document.getElementById(pId).value = "";
		document.getElementById(pId).focus();
	}
	else
	{
		var nId = pId.substring(1,2);
		nId = parseInt(nId);
		pValue = parseInt(pValue);
		nId = nId + 1;
		if(nId <= 5 && pValue < 11)
		{
			document.getElementById("f"+nId).value = pValue+1;
		}
		else
		{
		}
	}
}
function validateAgreementDetails()
{
	//alert("test");
	var result = true;
	var propertyType = document.getElementById('propertyType').value;
	var propertyUse = document.getElementById('propertyUse').value;
	var pcity = document.getElementById('pcity').value;
	var pPincode = document.getElementById('pPincode').value;
	var pregion = document.getElementById('pregion').value
	var pFromDate = document.getElementById('pFromDate').value;
	var pToDate = document.getElementById('pToDate').value;	
	if(propertyType == "")
	{	
		alert("Please select Property Type");
		document.getElementById('propertyType').focus();
		result = false;
	}
	if(propertyUse == "")
	{	
		alert("Please Select Property Use");
		document.getElementById('propertyUse').focus();
		result = false;
	}
	if(pcity == "")
	{	
		alert("Please Enter City");
		document.getElementById('propertyUse').focus();
		result = false;
	}
	if(pPincode == "")
	{	
		alert("Please Enter Pincode");
		document.getElementById('pPincode').focus();
		result = false;
	}
	if(pregion == "")
	{	
		alert("Please Enter Region");
		document.getElementById('pregion').focus();
		result = false;
	}
	if(pFromDate == "")
	{	
		alert("Please Enter Lease Start Date");
		document.getElementById('pFromDate').focus();
		result = false;
	}
	if(pToDate == "")
	{	
		alert("Please Enter Lease End Date");
		document.getElementById('pToDate').focus();
		result = false;
	}
	if(result)
	{
		saveAgreementDetails();
	}
}
function saveAgreementDetails()
{
	var tenantModuleId = document.getElementById("tenantId").value;
	//alert ("tenantModuleId "+tenantModuleId);
	var propertyType = document.getElementById("propertyType").value;
	var propertyUse = document.getElementById("propertyUse").value;
	var pAddress1 = document.getElementById("pAddress1").value;
	var pAddress2 = document.getElementById("pAddress2").value;
	var pPincode = document.getElementById("pPincode").value;
	var pcity = document.getElementById("pcity").value;
	var pregion = document.getElementById("pregion").value;
	var propertyArea = document.getElementById("propertyArea").value;
	var pFromDate = document.getElementById("pFromDate").value;
	//alert ("dob:"+dob);
	//var pmonth = document.getElementById("pmonth").value;
	var pToDate = document.getElementById("pToDate").value;
	var rentType = document.getElementById("rentType").value;
	var deposit = document.getElementById("deposit").value;
	var monthlyRent = document.getElementById("monthlyRent").value;
	var rentType = document.getElementById("rentType").value;
	//var pmonth = document.getElementById("pmonth").value;
	var unitId = document.getElementById("unitId").value;
	var view = document.getElementById("view").value;
	//alert ("gender:"+gender);
	$.ajax
	({
		url : "ajax/rentingRegistration.ajax.php",
		type : "POST",
		datatype: "JSON",
		data : {"method":"updateTenantDetails","tenantModuleId":tenantModuleId,"pFromDate":pFromDate,"pToDate":pToDate},
		success : function(data)
		{
			//alert (data);
			var tenantDetails=Array();
			tenantDetails=JSON.parse(data);
			var updateResult = String(tenantDetails['success']);
			if(updateResult == "1")
			{
				document.getElementById("btnSubmitDR").style.display = "inline";
				document.getElementById("btnSubmitAT").style.display = "none";
			}
			else
			{
				alert("Unable to connect. Please try again.")
			}
		}
	});
}
function sendDetailsToDigitalRenting()
{
	var tenantModuleId = document.getElementById("tenantId").value;
	//alert("tenantModuleId: "+tenantModuleId)
	var propertyType = document.getElementById("propertyType").value;
	var propertyUse = document.getElementById("propertyUse").value;
	var pAddress1 = document.getElementById("pAddress1").value;
	pAddress1=pAddress1.trim().replace(/ /g,'%20');
	var pAddress2 = document.getElementById("pAddress2").value;
	pAddress2=pAddress2.trim().replace(/ /g,'%20');
	var pPincode = document.getElementById("pPincode").value;
	var pcity = document.getElementById("pcity").value;
	var pregion = document.getElementById("pregion").value;
	var propertyArea = document.getElementById("propertyArea").value;
	var rentType = $('input[name=rentType]:checked').val();
	//alert ("rentType :"+rentType);
	
	var deposit = document.getElementById("deposit").value;
	var monthlyRent = "0";
	var i = 1;
	var monthlyRent = 0;
	var from1 = 0, from2 = 0, from3 = 0, rent1 = 0, rent2 = 0, rent3 = 0;
	if(rentType == "Fixed Rent")
	{
		monthlyRent = document.getElementById("monthlyRent").value;
	}
	else
	{
		from1 = document.getElementById("f1").value;
		from2 = document.getElementById("f2").value;
		from3 = document.getElementById("f3").value;
		rent1 = document.getElementById("r1").value;
		rent2 = document.getElementById("r2").value;
		rent3 = document.getElementById("r3").value;
	}
	rentType=rentType.trim().replace(/ /g,'%20');
	var unitId = document.getElementById("unitId").value;
	var view = document.getElementById("view").value;
	$.ajax
	({
		url : "ajax/rentingRegistration.ajax.php",
		type : "POST",
		datatype: "JSON",
		data : {"method":"sendDetailsToDigitalRenting","tenantModuleId":tenantModuleId,"propertyType":propertyType,"propertyUse":propertyUse,"pAddress1":pAddress1,"pAddress2":pAddress2,"pPincode":pPincode,"pcity":pcity,"pregion":pregion,"propertyArea":propertyArea,"rentType":rentType,"deposit":deposit,"monthlyRent":monthlyRent,"var1":from1,"var2":from2,"var3":from3,"rent1":rent1,"rent2":rent2,"rent3":rent3},
		success : function(data)
		{
			//alert (data);
			var tenantDetails=Array();
			tenantDetails=JSON.parse(data);
			var updateResult = String(tenantDetails['success']);
			if(updateResult == "1")
			{
				alert ("Data submitted Successfully...");
				window.location.href="viewRegistration.php?unitId="+unitId+"&View="+view;	
			}
			else
			{
				alert("Unable to connect. Please try again.")
			}
		}
	});
}
//window.location.href="viewAllTenant.php?unitId="+unitId+"&View="+view;	
//window.location.href='RentingRegistration.php?type=agreementTerms&unitId=<?php echo $_REQUEST['unitId']?>&View=<?php echo $_REQUEST['View'];?>'
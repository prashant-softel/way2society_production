var bIsSmsValidationDone = false;
var prohibited = ['property', '&','#','$','%','^','&','*','(',')','!','@','_','+','flat'];
var string = JSON.stringify(prohibited)
var newstring = string.replace (/"/g,'      ');

function get_unit(wing_id)
{
	if(wing_id == 0)
	{
		$('select#unit_id').empty();
		$('select#unit_id').append(
			$('<option></option>')
			.val('0')
			.html('All'));
	}
	else
	{
		document.getElementById('error').style.display = '';	
		document.getElementById('error').innerHTML = 'Fetching Units. Please Wait...';	
		populateDDListAndTrigger('select#unit_id', 'ajax/get_unit.php?getunit&wing_id=' + wing_id, 'unit', 'hide_error', true);
	}
}

function get_year()
{
	populateDDListAndTrigger('select#year_id', 'ajax/get_unit.php?getyear', 'year', 'get_period', false);
}

function get_period(year_id,period_index)
{
	document.getElementById('error').style.display = '';	
	document.getElementById('error').innerHTML = 'Fetching Period. Please Wait...';	
		
	if(year_id == null || year_id.length == 0)
	{
		//populateDDListAndTrigger('select#period_id', 'ajax/ajaxbill_period.php?getperiod&year=' + document.getElementById('year_id').value, 'period', 'periodFetched', false);
		populateDDListAndTrigger('select#period_id', 'ajax/ajaxbill_period.php?getperiod&year=' + document.getElementById('year_id').value, 'period', 'periodFetched', false, period_index);
	}
	else
	{
		populateDDListAndTrigger('select#period_id', 'ajax/ajaxbill_period.php?getperiod&year=' + year_id, 'period', 'periodFetched', false, period_index);
		//populateDDListAndTrigger('select#period_id', 'ajax/ajaxbill_period.php?getperiod&year=' + year_id, 'period', 'periodFetched', false);
	}
}

function periodFetched()
{
	hide_error();
	var periodID = document.getElementById('period_id').value;
}

function get_notes(periodid)
{
	var societyid = document.getElementById('society_id').value;

	var sURL = "ajax/ajaxgenbill.php";
	var obj = {'getnote':'getnote', 'society':societyid, 'period':periodid};
	remoteCallNew(sURL, obj, 'notefetched');
}

function notefetched()
{
	var sResponse = getResponse(RESPONSETYPE_STRING, true);
	//alert(sResponse);
	document.getElementById('bill_notes').value = sResponse;
}

function CheckRequestedNotification(IsGeneralPage)
{
	//Calling this methid from notification and gerenal page so for identification of pages, we pass the parameter as IsGeneralPage
	//alert("Check Register"+IsGeneralPage);
	var IsSMSChecked = document.getElementById('btn_all_sms').checked;
	var IsMobileChecked = document.getElementById('btn_all_notification').checked;
	if(IsGeneralPage == false)
	{
		var IsEmailChecked = document.getElementById('btn_all_email').checked;
		if(IsEmailChecked == true)
		{
			EMailSentAll();
		}
		if(IsSMSChecked == true)
		{
			SMSSentAll(1);
		}
		var IsRemEmailChecked = document.getElementById('btn_rem_email').checked;
		//alert( document.getElementById('pdfcount').value);
			var genPDFCnt = document.getElementById('pdfcount').value;
		var TotalunitCnt = document.getElementById('unitcount').value;
		
		if(IsRemEmailChecked == true)
		{
		  if(TotalunitCnt == genPDFCnt)
		  {
			  //alert("Equal");
			  RemEMailSentAll();
		  }
		  else
		  {
			  // alert("Not Equal");
			if (window.confirm("Do you still want to contiune?")) 
			{
				RemEMailSentAll();
  				//alert("Yes");
			}
			else
			{
				//alert("No");
			}
		  }
			//console.log("inside Rem email");
			//RemEMailSentAll();
		}
	}
	else if(IsGeneralPage == true)
	{
		if(IsSMSChecked == true)
		{
			GeneralSMSSentAll();
		}
	}
	if(IsMobileChecked == true)
	{
		sendMobileNotificationAllSelected(IsGeneralPage);
	}
}



function sendEmail(unitID)
{
	document.getElementById('status_' + unitID).innerHTML = 'Sending ...';
	
	var periodID = document.getElementById('period_id').value;
	var SentEmailManually = document.getElementById('SentEmailManually').value;
	var BillType = 0;
	if(document.getElementById('bill_method').value == '1')
	{
		BillType = 1;
	}
	
	$.ajax({
			url : "classes/email.class.php",
			type : "POST",
			data: { "unit":unitID, "period":periodID, "SentEmailManually":SentEmailManually, "BT": BillType} ,
			success : function(data)
			{	
				var unitsAry = JSON.parse(data);
      
				for(var objUnit in unitsAry)
				{
					console.log("result ",unitsAry[objUnit]);
          console.log("unit",objUnit);
					document.getElementById('status_'+objUnit).innerHTML = unitsAry[objUnit] ;
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

function sendEMailAll(UnitsArray)
{
	var periodID = document.getElementById('period_id').value;
	var SentEmailManually = document.getElementById('SentEmailManually').value;
	var BillType = 0;
	if(document.getElementById('bill_method').value == '1')
	{
		BillType = 1;
	}

	document.getElementById('btn_all_email').disabled = true;
	document.getElementById('btn_all_email').style.background = "grey";

	$.ajax({
			url : "classes/email.class.php",
			type : "POST",
			data: { "unitsArray":JSON.stringify(UnitsArray), "period":periodID, "SentEmailManually":SentEmailManually, "BT": BillType} ,
			success : function(data)
			{	
				var unitsAry = JSON.parse(data);
				for(var objUnit in unitsAry)
				{
					document.getElementById('status_'+objUnit).innerHTML = unitsAry[objUnit] ;
				}

				document.getElementById('btn_all_email').disabled = false;
				document.getElementById('btn_all_email').style.background = "white";
			},
				
			fail: function()
			{
				alert("failed");
			},
			
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
			}
		});

}

function sendSMS(unitID)
{
	//document.getElementById('status_' + unitID).innerHTML = 'Sending ...';
	var periodID = document.getElementById('period_id').value;
	var UnitsArray = "";
	
	/*var tmpArray = (unitID).split('#');*/

	UnitsArray = UnitsArray.concat(unitID).split('#');
	var SentSMSManually =  1;
	var BillType = 0;
	if(document.getElementById('bill_method').value == '1')
	{
		BillType = 1;
	}

	/*for(var iCnt = 0; iCnt < tmpArray.length; iCnt++)
	{
		if(tmpArray[iCnt].length > 0)
		{
			document.getElementById('send_sms_' + tmpArray[iCnt]).disabled = true;
			document.getElementById('send_sms_' + tmpArray[iCnt]).style.background = "grey";
		}
	}*/

	//document.getElementById('btn_all_sms').disabled = true;
	//document.getElementById('btn_all_sms').style.background = "grey";

	$.ajax({
			url : "classes/sms.class.php",
			type : "POST",
			data: {"period":periodID, "unitsArray":JSON.stringify(UnitsArray),"SentSMSManually":SentSMSManually, "BT": BillType} ,
			success : function(data)
			{
				//alert(data);	
				var unitsAry = JSON.parse(data);
				for(var objUnit in unitsAry)
				{
					//document.getElementById('status_'+objUnit).innerHTML = unitsAry[objUnit] ;				
					//alert("Unit:"+objUnit);
					if(unitsAry[objUnit].trim() == "Empty")
					{
						document.getElementById('status_' + objUnit).innerHTML = 'Invalid Mobile number.';
					}
					else if(unitsAry[objUnit].trim() == "Missing Parameters")
					{
						document.getElementById('status_' + objUnit).innerHTML = 'Error.';
					}
					else
					{					
						document.getElementById('status_' + objUnit).innerHTML = "Sent";
					}
					var sTarget = "sendsms_" + objUnit;
					window.open(unitsAry[objUnit], sTarget, "toolbar=no, scrollbars=yes, resizable=no, top=0, left=0, width=0, height=0");
				}

				document.getElementById('btn_all_sms').disabled = false;
				document.getElementById('btn_all_sms').style.background = "white";
				//window.open(data, "_blank", "toolbar=no, scrollbars=yes, resizable=yes, top=0, left=0");
				
			},
				
			fail: function()
			{
				
			},
			
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
			}
		});
}
function sendMobileNotificationAllSelected(IsGeneralPgRequest)
{
	//alert("test");
	var unitAry = document.getElementById('unit_ary').value.split('#');
	var unitAry2 = "";
	
	//document.getElementById('btn_all_email').disabled = true;
	//document.getElementById('btn_all_email').style.background = "grey";

	for(var iCnt = 0 ; iCnt < unitAry.length - 1 ; iCnt++)
	{
		if(document.getElementById('chk_' + unitAry[iCnt]).checked == true)
		{
			if(unitAry2 != 'undefined')
			{
				unitAry2 = unitAry2.concat(unitAry[iCnt] + "#");
				
				//alert(unitAry[iCnt]);
				document.getElementById('status_' + unitAry[iCnt]).innerHTML = 'Sending ...';
				//alert('argument '+IsGeneralPgRequest);
				console.log(IsGeneralPgRequest);
				
				if(IsGeneralPgRequest == true)
				{
					//alert('In General Notification');
					sendGeneralNotification(unitAry[iCnt])
				}
				else if(IsGeneralPgRequest == false)
				{
					//alert('In Bill');					
					sendMobileNotification(unitAry[iCnt]);
				}
			}
			else
			{
				unitAry2 =  unitAry[iCnt] + "#";
				
				document.getElementById('status_' + unitAry[iCnt]).innerHTML = 'Sending ...';
				
				if(document.getElementById('SendGeneralNotification').value = 1)
				{
					sendGeneralNotification(unitAry[iCnt])
				}
				else
				{
				sendMobileNotification(unitAry2.split('#'));
				}
			}
		}
	}
}
function sendMobileNotification(unitID)
{
	var periodID = document.getElementById('period_id').value;
	var UnitsArray = "";
	
	UnitsArray = UnitsArray.concat(unitID).split('#');
	//var UnitsArray = "";
	//UnitsArray = new Array(unitID); 
	//var SentSMSManually =  1;

	var periodName = document.getElementById('period_id').options[document.getElementById('period_id').selectedIndex].text;
	periodName = periodName.replace('**', '')
	var yearName = document.getElementById('year_id').options[document.getElementById('year_id').selectedIndex].text;
	
	

	var notificationMessage = "Generated Bill For " + periodName + " " + yearName; 
	var notificationTitle = "New Maintenance Bill";
	var BillType = 0;
	
	//alert(notificationMessage);

	if(document.getElementById('bill_method').value == '1')
	{
		notificationTitle = "New Supplementary Bill";
		BillType = 1;
	}
	var societyid = document.getElementById('society_id').value;
	//document.getElementById('btn_all_mobile_notification').disabled = true;
	//document.getElementById('btn_all_mobile_notification').style.background = "grey";

	$.ajax({
			url : "classes/android.class.php",
			type : "POST",
			data: {"period":periodID, 
					"unitsArray":JSON.stringify(unitID), 
					"SentNotificationManually": 1, 
					"billType": BillType,
					"title" : notificationTitle,
					"message" : notificationMessage,
					"dataType": 'json',
					"society" : societyid,
					"notifyFor" : 1},

			success : function(data)
			{
				//alert(data);	
				var unitsAry = JSON.parse(data);
				//alert(unitsAry.message);
				var unitsAry2 = JSON.parse(unitsAry.message);
				//alert(unitsAry2.success);
				$sResponse = "";
				if(unitsAry2.success == "1")
				{
					//alert('success');
					sResponse = "success";
				}
				//alert(unitsAry);	
				document.getElementById('status_' + unitID).innerHTML = sResponse;
				

					//var sTarget = "sendsms_" + objUnit;
					//window.open(unitsAry[objUnit], sTarget, "toolbar=no, scrollbars=yes, resizable=no, top=0, left=0, width=0, height=0");
				

				//document.getElementById('btn_all_mobile_notification').disabled = false;
				//document.getElementById('btn_all_mobile_notification').style.background = "white";
				//window.open(data, "_blank", "toolbar=no, scrollbars=yes, resizable=yes, top=0, left=0");
				
			},
				
			fail: function()
			{
				
			},
			
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
			}
		});
}


function sendGeneralNotification(unitID)
{
	var WingID = document.getElementById('wing_id').value;
	var notificationMessage = document.getElementById('description').value; 
	var notificationTitle = "General Notification";
	var societyid = document.getElementById('society_id').value;
	$.ajax({
			url : "classes/android.class.php",
			type : "POST",
			data: {"WingID":WingID, 
					"unitsArray":JSON.stringify(unitID), 
					"SentNotificationManually": 1, 
					"title" : notificationTitle,
					"message" : notificationMessage,
					"dataType": 'json',
					"society" : societyid,
					"notifyFor" : 1},

			success : function(data)
			{
				//alert(data);	
				var unitsAry = JSON.parse(data);
				//alert(unitsAry.message);
				var unitsAry2 = JSON.parse(unitsAry.message);
				//alert(unitsAry2.success);
				$sResponse = "";
				if(unitsAry2.success == "1")
				{
					alert('success');
					sResponse = "success";
				}
				//alert(unitsAry);	
				document.getElementById('status_' + unitID).innerHTML = sResponse;
				

					//var sTarget = "sendsms_" + objUnit;
					//window.open(unitsAry[objUnit], sTarget, "toolbar=no, scrollbars=yes, resizable=no, top=0, left=0, width=0, height=0");
				

				//document.getElementById('btn_all_mobile_notification').disabled = false;
				//document.getElementById('btn_all_mobile_notification').style.background = "white";
				//window.open(data, "_blank", "toolbar=no, scrollbars=yes, resizable=yes, top=0, left=0");
				
			},
				
			fail: function()
			{
				
			},
			
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
			}
		});
}


function sendGeneralSMS(UnitID)
{
	var MsgBody = document.getElementById('description').value.trim();
	var bMsgContainsInvalidCharacters = bIsSmsContainsInvalidText(MsgBody); 
	
	if(MsgBody == "")
	{
		document.getElementById('error').innerHTML = "Please Enter some message to send.";
		return;	
	}
	else if(MsgBody != "" && bMsgContainsInvalidCharacters == true && bIsSmsValidationDone == false)
	{
		document.getElementById('error').innerHTML = "<img src='images/del.gif' /> " + newstring + "  All these words/characters are not allowed in sms.";
		//$('#error').prepend('<img  src="images/del.gif" />');
		window.scroll(0, 0);
		return;	
	}
	else
	{
		document.getElementById('error').innerHTML = "";
	}
	//document.getElementById('status_' + UnitID).innerHTML = 'Sending ...';	
	var UnitsArray = "";
	//alert(UnitID);
	UnitsArray = UnitsArray.concat(UnitID).split('#');
	var SentSMSManually =  1;
	
	$.ajax({
			url : "classes/generalSms.class.php",
			type : "POST",
			data: { "unitsArray":JSON.stringify(UnitsArray), "msgBody":MsgBody,"SentSMSManually":SentSMSManually} ,
			success : function(data)
			{
				//alert(data);					
				var unitsAry = JSON.parse(data);
				for(var objUnit in unitsAry)
				{
					//document.getElementById('status_'+objUnit).innerHTML = unitsAry[objUnit] ;				
					//alert("Unit:"+objUnit);
					if(unitsAry[objUnit].trim() == "Empty")
					{
						document.getElementById('status_' + objUnit).innerHTML = 'Invalid Mobile number.';
					}
					else if(unitsAry[objUnit].trim() == "Missing Parameters")
					{
						document.getElementById('status_' + objUnit).innerHTML = 'Error.';
					}
					else
					{					
						document.getElementById('status_' + objUnit).innerHTML = "Sent";
					}
				}
					//var sTarget = "sendsms_" + objUnit;
					//window.open(unitsAry[objUnit], sTarget, "toolbar=no, scrollbars=yes, resizable=no, top=0, left=0, width=0, height=0");
				//window.open(data, sTarget, "toolbar=no, scrollbars=yes, resizable=no, top=0, left=0, width=0, height=0");												
			},
				
			fail: function()
			{
				
			},
			
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
			}
		});	
}

function SelectAll(chkBox)
{
	//alert("Select All" + chkBox.checked);
	var unitAry = document.getElementById('unit_ary').value.split('#');
	for(var iCnt = 0 ; iCnt < unitAry.length - 1 ; iCnt++)
	{
		document.getElementById('chk_' + unitAry[iCnt]).checked = chkBox.checked;
	}
}

function EMailSentAll()
{
	var unitAry = document.getElementById('unit_ary').value.split('#');
	var unitAry2 = "";
	
	document.getElementById('btn_all_email').disabled = true;
	document.getElementById('btn_all_email').style.background = "grey";

	for(var iCnt = 0 ; iCnt < unitAry.length - 1 ; iCnt++)
	{
		if(document.getElementById('chk_' + unitAry[iCnt]).checked == true)
		{
			if(unitAry2 != 'undefined')
			{
				unitAry2 = unitAry2.concat(unitAry[iCnt] + "#");
				
				document.getElementById('status_' + unitAry[iCnt]).innerHTML = 'Sending ...';
			}
			else
			{
				unitAry2 =  unitAry[iCnt] + "#";
				
				document.getElementById('status_' + unitAry[iCnt]).innerHTML = 'Sending ...';
			}
		}
	}
	sendEMailAll(unitAry2.split('#'));
}

function SMSSentAll(bIsMobileNotification)
{
	var unitAry = document.getElementById('unit_ary').value.split('#');
	var unitAry2 = "";
	//bIsMobileNotification = 1;
	if(bIsMobileNotification == 1)
	{
		document.getElementById('btn_all_sms').disabled = true;
		document.getElementById('btn_all_sms').style.background = "grey";
	}
	else
	{
		//document.getElementById('btn_all_mobile_notification').disabled = true;
		//document.getElementById('btn_all_mobile_notification').style.background = "grey";	
	}

	for(var iCnt = 0 ; iCnt < unitAry.length - 1 ; iCnt++)
	{
		if(document.getElementById('chk_' + unitAry[iCnt]).checked == true)
		{
			if(unitAry2 != 'undefined')
			{
				unitAry2 = unitAry2.concat(unitAry[iCnt] + "#");
				
				document.getElementById('status_' + unitAry[iCnt]).innerHTML = 'Sending ...';
			}
			else
			{
				unitAry2 =  unitAry[iCnt] + "#";
				
				document.getElementById('status_' + unitAry[iCnt]).innerHTML = 'Sending ...';
			}
		}
	}
	//alert(unitAry2);
	if(bIsMobileNotification == 1)
	{
		sendSMS(unitAry2);
	}
	else
	{
		sendMobileNotification(unitAry2);
	}
}

function SMSSent()
{
	var response = getResponse(RESPONSETYPE_STRING, true);
	alert(response);
}

function GeneralSMSSentAll()
{	
	var MsgBody = document.getElementById('description').value.trim();
	var bMsgContainsInvalidCharacters = bIsSmsContainsInvalidText(MsgBody); 
	
	if(MsgBody == "")
	{
		document.getElementById('error').innerHTML = "Please Enter some message to send.";
		return;	
	}
	else if(MsgBody != "" && bMsgContainsInvalidCharacters == true)
	{
		document.getElementById('error').innerHTML = "<img src='images/del.gif' />  " + newstring + "  All these words/characters are not allowed in sms.";
		window.scroll(0, 0);
		return;	
	}
	else
	{
		document.getElementById('error').innerHTML = "";
	}
	bIsSmsValidationDone = true;
	var unitAry2 = "";
	var unitAry = document.getElementById('unit_ary').value.split('#');
	for(var iCnt = 0 ; iCnt < unitAry.length - 1 ; iCnt++)
	{
		if(document.getElementById('chk_' + unitAry[iCnt]).checked == true)
		{
			
			if(unitAry2 != 'undefined')
			{
				unitAry2 = unitAry2.concat(unitAry[iCnt] + "#");
				
				document.getElementById('status_' + unitAry[iCnt]).innerHTML = 'Sending ...';
			}
			else
			{
				unitAry2 =  unitAry[iCnt] + "#";
				
				document.getElementById('status_' + unitAry[iCnt]).innerHTML = 'Sending ...';
			}
		}
	}
	
					
	sendGeneralSMS(unitAry2);
	bIsSmsValidationDone = false;
	
}

function bIsSmsContainsInvalidText(smsText)
 {
 
 	  var smsText = smsText.toLowerCase(); 	
	  for (var i = 0; i < prohibited.length; i++) 
	  {
			if(smsText.includes(prohibited[i]) == true)
			{
				return true;	
			}
			
	  }
  return false;
}
function Fxposition()
{
	//alert("Bingo");
}

function SendTest()
{
	var msgContent = document.getElementById('description').value;
	if(msgContent == '')
	{
		alert("Please first select SMS Template");
	}
	else
	{
		var userMobileNo = document.getElementById('userMobileNo').value;

		var TestMobileNo = prompt("Please Enter the Mobile Number",userMobileNo);
		if(TestMobileNo != '')
		{
			$.ajax
				({
					url : "ajax/ajaxcommon.ajax.php",
					type : "POST",
					data : {"method":"SMSTest", "TestMobileNo":TestMobileNo, "SMSTemplate":msgContent},
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
	}
}

function RemEMailSentAll()
{
	var unitAry1 = document.getElementById('unit_ary').value.split('#');
	var unitAry3 = "";
	console.log(unitAry1);
	document.getElementById('btn_rem_email').disabled = true;
	document.getElementById('btn_rem_email').style.background = "grey";
	for(var icount = 0 ; icount < unitAry1.length - 1 ; icount++)
	{
		console.log(document.getElementById('chk_' + unitAry1[icount]).checked);
		if(document.getElementById('chk_' + unitAry1[icount]).checked == true)
		{
			if(unitAry3 != 'undefined')
			{
				unitAry3 = unitAry3.concat(unitAry1[icount] + "#");
				document.getElementById('status_' + unitAry1[icount]).innerHTML = 'Sending ...';
			}
			else
			{
				unitAry3 =  unitAry1[icount] + "#";
				document.getElementById('status_' + unitAry1[icount]).innerHTML = 'Sending ...';
			}
		}
	}
	console.log(unitAry3.split('#'));
	//return false;
	if(unitAry3.split('#').length == 1 || unitAry3.split('#').length == 0)
	{
		alert("Please select Units before sending email");
		return false;
	}
	else
	{
		sendRemEMailAll(unitAry3.split('#'));
	}
	
	
	//}
}
function sendRemEMailAll(UnitsArray)
{
	var periodID = document.getElementById('period_id').value;
	var SentEmailManually = document.getElementById('SentEmailManually').value;
	var BillType = 0;
	if(document.getElementById('bill_method').value == '1')
	{
		BillType = 1;
	}

	document.getElementById('btn_rem_email').disabled = true;
	document.getElementById('btn_rem_email').style.background = "grey";
	//console.log("Period ID :=>" +periodID+"SendBIllManual=>"+SentEmailManually+"BIllType=>"+BillType);
	$.ajax({
			url : "classes/email.class.php",
			type : "POST",
			data: {"method":"SendRemEMail", "unitsArray":JSON.stringify(UnitsArray), "period":periodID, "BT": BillType} ,
			success : function(data)
			{	
			console.log(data);
			//if(data == 1)
			//{
				//document.getElementById('status_'+objUnit).innerHTML ='Success';
			//}
			//else
			//{
			//	document.getElementById('status_'+objUnit).innerHTML ='Feild';
			//}
				var unitsAry = JSON.parse(data);
			for(var objUnit in unitsAry)
				{
				document.getElementById('status_'+objUnit).innerHTML = unitsAry[objUnit] ;
			}

				document.getElementById('btn_rem_email').disabled = false;
				document.getElementById('btn_rem_email').style.background = "white";
			},
				
			fail: function()
			{
				alert("failed");
			},
			
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
			}
		});

}
function getadd_member_id(str)
{
	var iden=new Array();
	iden=str.split("-");

	if(iden[0]=="delete")
	{
		var d=confirm("Are you sure , you want to delete it ???");
		if(d==1)
		{
			remoteCall("ajax/ajaxadd_member_id.php","form=add_member_id&method="+iden[0]+"&add_member_idId="+iden[1],"loadchanges");
		}
	}
	else
	{
			remoteCall("ajax/ajaxadd_member_id.php","form=add_member_id&method="+iden[0]+"&add_member_idId="+iden[1],"loadchanges");
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
		document.getElementById('com_id').value=arr2[1];
		document.getElementById('member_id').value=arr2[2];
		document.getElementById('password').value=arr2[3];
		document.getElementById("id").value=arr2[0];
		document.getElementById("insert").value="Update";
	}
	else if(arr1[0] == "delete")
	{
		window.location.href ="../add_member_id.php";
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


function emailPromtWindow(sEmailId,sName,sCode) 
{
	if (sEmailId == "")
	{
			sEmailId = "abc@way2society.com";
	}
	var emailID = prompt("Please enter Email ID",  sEmailId);
	
	if (emailID != null)
	{				
		/*var sURL = "ajax/ajaxmanage_users.php";
		var obj = {"method" : "fetchEmail","email" : sEmailId,"name":sName , "code":sCode};
		remoteCallNew(sURL, obj, 'emailPreview()');	*/
		
		
		$.ajax({
			url : "ajax/ajaxmanage_users.php",
			type : "POST",
			data : {"method" : "fetchEmail","email" : emailID,"name":sName , "code":sCode},
			success : function(data)
			{	
				//hideLoader();	
				emailPreview(data,emailID,sName ,sCode);
				
			},
				
			fail: function()
			{
				//hideLoader();	
			},
			
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
				//hideLoader();
			}
		});		
	}
}

function ToggleCheckAllEmail()
{
	$(".chckbxUnits").prop('checked', false);
	var UnitsCheckboxes = document.getElementsByClassName('chckbxUnits'), i;
	var bCheckStatus = document.getElementById("checkall").checked;
	//alert(bCheckStatus);
	var iUnitsToggled = 0;
	for (i = 0; i < UnitsCheckboxes.length; i += 1)
	{
		var sEmailID = UnitsCheckboxes[i].id;
		var sName = UnitsCheckboxes[i].name;
		//alert(UnitsCheckboxes[i].checked);
		if(sEmailID.length > 0 )
		{
			//if(UnitsCheckboxes[i].checked)
			//{
				//UnitsCheckboxes[i].checked = false;
				//document.getElementById(UnitsCheckboxes[i].id).checked = bCheckStatus;
				
				if(bCheckStatus)
				{
					//alert('checked');
					$(UnitsCheckboxes[i]).closest('tr').find('input:checkbox:first').attr('checked', true);
					document.getElementById(UnitsCheckboxes[i].id).checked = true;
				}
				else
				{
					$(UnitsCheckboxes[i]).closest('tr').find('input:checkbox:first').attr('checked', false);
					document.getElementById(UnitsCheckboxes[i].id).checked = false;
				}
			iUnitsToggled++; 
		}
		else
		{
			$(UnitsCheckboxes[i]).closest('tr').find('div').text('Email ID is not set');
			$(UnitsCheckboxes[i]).closest('tr').find('div'). css("color", "black");
			
			
			//document.getElementById('lbl').innerHTML  = "Email ID is not set";
			
			//$(':div').closest('tr').find('div').text('Email ID is not set');
			//alert(UnitsCheckboxes[i]);
			//var demo = $(UnitsCheckboxes[i]).closest('tr').find('p')[0].innerHTML + 'test';
			//$(UnitsCheckboxes[i]).closest('tr').find('div').text(demo);
			
			//$('lbl').hide().html('Email ID is not set').fadeIn('slow').delay(5000).hide();
		}
	}
	if(bCheckStatus)
	{
		alert( iUnitsToggled  + ' unit(s) selected.');
		document.getElementById("SendAllTop").style.display = 'block';
		
		document.getElementById("SendAll").style.display = 'block';
	}
	
}

function ToggleCheckAllSMS()
{
	$(".chckbxUnits").prop('checked', false);

	var UnitsCheckboxes = document.getElementsByClassName('chckbxUnits'), i;
	var bCheckStatus = document.getElementById("checkallSMS").checked;
	//alert(UnitsCheckboxes);
	var iUnitsToggled = 0;
	for (i = 0; i < UnitsCheckboxes.length; i += 1)
	{
		var sEmailID = UnitsCheckboxes[i].id;
		var sValue = UnitsCheckboxes[i].value;
		var sName = UnitsCheckboxes[i].name;

		if(sValue.length>0 )
		{
			if(sValue !='0' && sValue !='')
			{
				// alert(sValue);
			
				if(bCheckStatus)
				{
					$(UnitsCheckboxes[i]).closest('tr').find('input:checkbox:first').attr('checked', true);
					document.getElementById(UnitsCheckboxes[i].id).checked = true;
				}
				else
				{
					$(UnitsCheckboxes[i]).closest('tr').find('input:checkbox:first').attr('checked', false);
					document.getElementById(UnitsCheckboxes[i].id).checked = false;
				}
			iUnitsToggled++; 
		   }
		   else
		   {
		   	$(UnitsCheckboxes[i]).closest('tr').find('div').text('Mobile Number Not Set');
			$(UnitsCheckboxes[i]).closest('tr').find('div'). css("color", "black");

		   }
		}
		else
		{
			$(UnitsCheckboxes[i]).closest('tr').find('div').text('Mobile Number Not Set');
			$(UnitsCheckboxes[i]).closest('tr').find('div'). css("color", "black");
			
		}
	}

	if(bCheckStatus)
	{
		alert( iUnitsToggled  + ' unit(s) selecteddd.');
		document.getElementById("SendAllTop").style.display = 'block';
		
		document.getElementById("SendAll").style.display = 'block';
	}
	
}

function showLoader()
{
	$(".loader").fadeIn("slow");
}
	
function hideLoader()
{
	$(".loader").fadeOut("slow");
}
	
function hideLoaderFast()
{
	$(".loader").fadeOut("fast");
}
	
function Send_invitaion_to_all_member()
{
	showLoader();
	$.ajax({
			url : "classes/email.class.php",
			type : "POST",
			data : {"method" : "Send_invitaion_to_all_member"},
			success : function(data)
			{	
				console.log(data);
				
				var result = data.split('@@@');
				//return false;
				if(result[1] != null)
				{
					var EmailResult = JSON.parse(result[1]);
					
					var response = "<table id='classTable' class='table table-bordered table-responsive'><th>Sr No.</th><th>Unit No.</th><th>Member Name</th><th>Email ID</th><th>Status</th>";
						
						var Cnt = 1;
						for(var i = 0; i < EmailResult.length; i++)
						{
							response  += "<tr><td>"+Cnt+"</td><td>"+EmailResult[i]['unit_no']+"</td><td>"+EmailResult[i]['Name']+"</td><td>"+EmailResult[i]['email']+"</td><td>"+EmailResult[i]['status']+"</td></tr>"
							Cnt++;
						}
					response += "</table>";
				}
				else
				{
					response = "<h2>Something went wrong please try again</h2>"
					
				}
				console.log("response",response);
				$('.modal-body').empty();
				hideLoader();	
				$('.modal-body').append(response);
				$('.EmailResponse').modal('show');
				
				
				//emailPreview(data,emailID,sName ,sCode);
				
			},
				
			fail: function()
			{
				//hideLoader();	
			},
			
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
				//hideLoader();
			}
		});		
}

function CheckSelected()
{
	if($("input:radio[name='checkall']").is(":checked")) {

		var selected_radio =$("input[name='checkall']:checked").val();
		if(selected_radio=="Email")
		{
			SendAll();
		}
		else if(selected_radio=="SMS")
		{
			SendAllSMS();
		}

     }
     else
     {
     	alert('Please Select Any One Option ')
     }
}

function SendAll()
{
	//alert('start');
	var UnitsCheckboxes = document.getElementsByClassName('chckbxUnits'), i;
	//alert(UnitsCheckboxes.length);
	for (i = 0; i < UnitsCheckboxes.length; i += 1)
	{
		var sEmailID = UnitsCheckboxes[i].id;
		var sName = UnitsCheckboxes[i].name;
		var sResponse = "";
		//alert(UnitsCheckboxes[i].checked);
		if(sEmailID.length > 0 && UnitsCheckboxes[i].checked )
		{
			//sendAccountActivationEmail(sEmailID ,sName , 0);
			//alert(sEmailID);
			var sCode = $(UnitsCheckboxes[i]).closest('tr').find('p')[0].innerHTML;
			//alert($(UnitsCheckboxes[i]).closest('tr').find('p').text());
			document.getElementById('lbl'+sEmailID).innerHTML = 'Sending ...';
			$.ajax({
			url : "ajax/ajaxmanage_users.php",
			type : "POST",
			data : {"method" : "sendEmail","email" : sEmailID,"name":sName , "code":sCode},
			success : function(data)
			{	
				//hideLoader();	
				data = data.replace(/\s+/, "") ;
				var iIndex = data.lastIndexOf(":");
				if(iIndex >= 0)
				{
					var sEmail = data.substr(0, iIndex);
					var sResult = data.substr(iIndex + 1);
					//sResponse = data;
					//alert('email:'+sEmail);
					//alert('result:'+sResult);
					document.getElementById('lbl'+sEmail).innerHTML = sResult;
					if(sResult == 'Success')
					{
						document.getElementById('lbl'+sEmail).style.color = "green";
					}
					else
					{
						document.getElementById('lbl'+sEmail).style.color = "red";
					}
				}
				//$(UnitsCheckboxes[i]).closest('tr').find('label').text('data);
				
			},
				
			fail: function()
			{
				hideLoader();	
			},
			
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
				hideLoader();
			}
			});	
			//alert(sEmailID);
			
		}
	}
}

function SendAllSMS()
{
	//alert('start');
	var UnitsCheckboxes = document.getElementsByClassName('chckbxUnits'), i;
	// alert(UnitsCheckboxes.length);
	for (i = 0; i < UnitsCheckboxes.length; i += 1)
	{
		var sEmailID = UnitsCheckboxes[i].id;
		var sName = UnitsCheckboxes[i].name;
		var sValue = UnitsCheckboxes[i].value;
		var sResponse = "";
		//alert(UnitsCheckboxes[i].checked);
		if(sValue.length > 0 && UnitsCheckboxes[i].checked )
		{
			var sCode = $(UnitsCheckboxes[i]).closest('tr').find('p')[0].innerHTML;
			document.getElementById('lbl'+sEmailID).innerHTML = 'Sending ...';
			$.ajax({
			url : "ajax/ajaxmanage_users.php",
			type : "POST",
			data : {"method" : "sendSMS","mobile" : sValue,"name":sName , "code":sCode},
			success : function(data)
			{	
				   var sResult = data.replace(/\s+/, "") ;

					document.getElementById('lbl'+sEmailID).innerHTML = sResult;
					if(sResult == 'success')
					{
						document.getElementById('lbl'+sEmailID).style.color = "green";
					}
					else
					{
						document.getElementById('lbl'+sEmailID).style.color = "red";
					}				
			},
				
			fail: function()
			{
				hideLoader();	
			},
			
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
				hideLoader();
			}
			});	
			//alert(sEmailID);
			
		}
	}
}

function emailPreview(sResponse,sEmailId,sName ,sCode) 
{
	
	//var sResponse = getResponse(RESPONSETYPE_STRING, true);
	var sMsg = sResponse;
	var sText = '<a href="#close" title="Close" class="close" id="close" onClick="closeDialogBox();">X</a>';
	sText += '<center><font style="font-size:18px;"><b>Preview Email</b></center></font>' + sMsg + '<br/>';
	var sButton = '<br/><center><button name="Send" class="closeButton" id="dialogYesNo_yes"  onClick="sendAccountActivationEmail(\''+sEmailId +'\',\''+sName +'\',\''+sCode+'\',this);">Send Activation Email To User</button>';
	sButton += '<button name="Close" class="closeButton" id="dialogYesNo_no"  onClick="closeDialogBox();">Close</button></center>';
	
	document.getElementById('message_ok').innerHTML = sText;
	document.getElementById('message2').innerHTML = sButton;
	$( document.body ).css( 'pointer-events', 'none' );
	document.getElementById('openDialogOk').style.opacity = 1;
	$('#openDialogOk').css( 'pointer-events', 'auto' );
}


function closeDialogBox()
{
		document.getElementById('openDialogOk').style.opacity = 0;
		$( document.body ).css( 'pointer-events', 'auto' );
		$('#openDialogOk').css( 'pointer-events', 'none' );
				
}

function sendAccountActivationEmail(sEmailId ,sName , sCode ,obj)
{
	 document.getElementById(obj.id).innerHTML="Sending Email Please Wait...";
	 document.getElementById(obj.id).disabled=true;
	// document.getElementById(obj.id).value="Sending Email Please Wait...";
 
	showLoader();
	$.ajax({
			url : "ajax/ajaxmanage_users.php",
			type : "POST",
			data : {"method" : "sendEmail","email" : sEmailId,"name":sName , "code":sCode},
			success : function(data)
			{	
				hideLoader();	
				//alert(data);
				data = data.replace(/\s+/, "") ;
				var iIndex = data.lastIndexOf(":");
				if(iIndex >= 0)
				{
					var sEmail = data.substr(0, iIndex);
					var sResult = data.substr(iIndex + 1);
					//sResponse = data;
					//alert('email:'+sEmail);
					alert('Email Sent status : '+sResult);
					document.getElementById(obj.id).style.display ='none';
				}
				
			},
				
			fail: function()
			{
				hideLoader();	
			},
			
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
				hideLoader();
			}
		});		
}
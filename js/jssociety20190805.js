function ShowReminder()
{
	var sendReminder = document.getElementById('send_reminder').checked;
	if(sendReminder == true)
	{
		document.getElementById('ReminderID').style.display = 'block';
	}
	else
	{
		document.getElementById('ReminderID').style.display = "none";		
	}
}
function ShowReminderEmail()
{
	var sendReminder = document.getElementById('Send_reminder_email').checked;
	if(sendReminder == true)
	{
		document.getElementById('ReminderIDEmail').style.display = 'block';
	}
	else
	{
		document.getElementById('ReminderIDEmail').style.display = "none";		
	}
}

function ShowNoteEmail()
{
	var ReminderValue = document.getElementById('reminder_days_email').value;
	if(ReminderValue == 0)
	{
		document.getElementById('Note1').style.display = "Block";	
		document.getElementById('Note1').innerHTML = '(Note* :0 Means the due date)';
	}
	else  
	{
		document.getElementById('Note1').style.display = "none";
	}
	document.getElementById('IsSetSMSChange').value = 1;
	//document.getElementById('SetSMSValue').value = ReminderValue;
	
	//return ReminderValue;
	
}

function ShowNote()
{
	var ReminderValue = document.getElementById('reminder_days').value;
	if(ReminderValue == 0)
	{
		document.getElementById('Note').style.display = "Block";	
		document.getElementById('Note').innerHTML = '(Note* :0 Means the due date)';
	}
	else
	{
		document.getElementById('Note').style.display = "none";
	}
	document.getElementById('IsSetSMSChange').value = 1;
	//document.getElementById('SetSMSValue').value = ReminderValue;
	
	//return ReminderValue;
	
}

function getsociety(str)
{
	var iden=new Array();
	iden=str.split("-");

	if(iden[0]=="delete")
	{
		var d=confirm("Are you sure , you want to delete it ???");
		if(d==1)
		{
			remoteCall("ajax/ajaxsociety.php","form=society&method="+iden[0]+"&societyId="+iden[1],"loadchanges");
		}
	}
	
	else if(iden[0]=="edit")
	{
		//alert("edit");
		
			remoteCall("ajax/ajaxsociety.php","form=society&method="+iden[0]+"&societyId="+iden[1],"loadchanges");
	}
	else if(iden[0]=="show")
	{
		//alert("show");
		
			remoteCall("ajax/ajaxsociety.php","form=society&method="+iden[0]+"&societyId="+iden[1],"loadchanges");
	}
}

function society_edit(str)
{
	window.location.href = "society.php?id="+str+"&tokufj=475958&edt&imp";	
}

function loadchanges()
{
	
	//alert('loadchanges');
	var a=trim(sResponse);
	var arr1=new Array();
	var arr2=new Array();
	arr1=a.split("@@@");
	arr2 = JSON.parse("["+arr1[1]+"]");
	var response = arr2[0][0];
	
	if(arr1[0] == "edit")
	{
		
		document.getElementById("society_code").value=response.society_code;
		document.getElementById("society_code").readOnly='readOnly';	
		document.getElementById("society_name").value=response.society_name;
	    document.getElementById("circle").value=response.circle;
		document.getElementById("registration_date").value=response.registration_date;
		document.getElementById("registration_no").value=response.registration_no;
		document.getElementById("society_add").value=response.society_add;
		document.getElementById("city").value=response.city;
		document.getElementById("landmark").value=response.landmark;
		document.getElementById("state_id").value=response.state;
		document.getElementById("region").value=response.region;
		document.getElementById("postal_code").value=response.postal_code;	
		document.getElementById("phone").value=response.phone;
		document.getElementById("phone2").value=response.phone2;
		document.getElementById("fax_number").value=response.fax_number;
		document.getElementById("pan_no").value=response.pan_no;
		document.getElementById("tan_no").value=response.tan_no;
		document.getElementById("service_tax_no").value=response.service_tax_no;
		document.getElementById("email").value=response.email;
		document.getElementById("url").value=response.url;
		document.getElementById("member_since").value=response.member_since;
		document.getElementById("bill_cycle").value=response.bill_cycle;
		document.getElementById("int_rate").value=response.int_rate;
		document.getElementById("int_tri_amt").value=response.int_tri_amt;
		document.getElementById("int_method").value=response.int_method;
		document.getElementById("rebate_method").value=response.rebate_method;
		document.getElementById("rebate").value=response.rebate;
		document.getElementById("chq_bounce_charge").value=response.chq_bounce_charge;
		document.getElementById("bill_method").value=response.bill_method;
		document.getElementById("wing").checked= (response.show_wing == 1) ? true : false;
		document.getElementById("parking").checked= (response.show_parking == 1) ? true : false;
		document.getElementById("area").checked=(response.show_area == 1) ? true : false;
		document.getElementById("calc_int").checked=(response.calc_int == 1) ? true : false;
		document.getElementById("property_tax_no").value=response.property_tax_no;
		document.getElementById("water_tax_no").value=response.water_tax_no;
		document.getElementById("share").checked=(response.show_share == 1) ? true : false;
		document.getElementById('bill_footer').value = response.bill_footer;
		document.getElementById('sms_start_text').value = response.sms_start_text;
		document.getElementById('sms_end_text').value = response.sms_end_text;
		document.getElementById('send_reminder').checked = (response.send_reminder_sms == 1) ? true : false;
		document.getElementById('bill_due_date').checked = (response.bill_due_date == 1) ? true : false;
		document.getElementById('show_floor').checked = (response.show_floor == 1) ? true : false;
		document.getElementById("unit_presentation").value=response.unit_presentation;
		document.getElementById("unit_presentation_previous_value").value=response.unit_presentation;
		document.getElementById('cc_email').value = response.cc_email;
		document.getElementById('bill_as_link').checked = (response.bill_as_link == 1) ? true : false;
		document.getElementById('email_contactno').value = response.email_contactno;
		document.getElementById('neft_notify_by_email').checked = (response.neft_notify_by_email == 1) ? true : false;
		document.getElementById('show_address_in_email').checked = (response.show_address_in_email == 1) ? true : false;
		document.getElementById('show_logo').checked = (response.show_logo == 1) ? true : false;	
		document.getElementById('show_QR_code').checked = (response.show_QR_code == 1) ? true : false;	
		document.getElementById('print_voucher_portrait').checked = (response.print_voucher_portrait == 1) ? true : false;	
		document.getElementById('apply_service_tax').checked = (response.apply_service_tax == 1) ? true : false;
		document.getElementById('service_tax_threshold').value = response.service_tax_threshold;
		document.getElementById('igst_tax_rate').value = response.igst_tax_rate;
		document.getElementById('cgst_tax_rate').value = response.cgst_tax_rate;
		document.getElementById('sgst_tax_rate').value = response.sgst_tax_rate;
		document.getElementById('cess_tax_rate').value = response.cess_tax_rate;
		document.getElementById('gstin_no').value = response.gstin_no;
		document.getElementById('apply_GST_On_Interest').checked = (response.apply_GST_On_Interest == 1) ? true : false;
		document.getElementById('apply_GST_above_Threshold').checked = (response.apply_GST_above_Threshold == 1) ? true : false;
		document.getElementById('gst_start_date').value=response.gst_start_date;
		document.getElementById('bank_penalty_amt').value = response.bank_penalty_amt;
		document.getElementById('notify_payment_voucher_daily_by_email').checked = (response.notify_payment_voucher_daily == 1) ? true : false;
		document.getElementById('notify_payment_voucher_daily_by_sms').checked = (response.notify_sms_payment_voucher_daily == 1) ? true : false;
		document.getElementById('balancesheet_temp').value=response.balancesheet_template;
		//Here We checking whether Send reminder is checked or not and according to that we are showing set reminder setup
		if(document.getElementById('send_reminder').checked == true)
		{
			document.getElementById('reminder_days').value = response.SMS_Reminder_Days;
		}
		else
		{
			document.getElementById('ReminderID').style.display = "none";
		}
		if(response.society_logo_thumb == '')
			{
				//document.getElementById('logomsg').style.display = 'block';
			}
			else
			{
				//document.getElementById('logomsg').style.display = 'none';
				document.getElementById('logoImg').innerHTML = '';
			}
			
			
		//document.getElementById('apply_NEFT_member').checked = (arr2[62] == 1) ? true : false;
		document.getElementById('apply_NEFT_member').checked = (response.Record_NEFT == 1) ? true : false;	
		document.getElementById('Send_reminder_email').checked = (response.send_reminder_email == 1) ? true : false;	
		document.getElementById('reminder_days_email').value = response.Email_Reminder_Days;
		var enable_Paytm = document.getElementById('enable_paytm').checked = (response.PaymentGateway == 1) ? true : false;
		if(enable_Paytm == true)
		{
			document.getElementById('pg_name_tr').style.display='table-row';
			document.getElementById('pg_link_tr').style.display='table-row';
			document.getElementById('payment_bank_tr').style.display='table-row';
			document.getElementById('pg_link').value = response.Paytm_Link;
			document.getElementById('pg_name').value = response.PGName;	
			document.getElementById('payment_bank').value = response.PGBeneficiaryBank;
		}
		else
		{
			document.getElementById('pg_name_tr').style.display='none';
			document.getElementById('pg_link_tr').style.display='none';
			document.getElementById('payment_bank_tr').style.display='none';
		}
		document.getElementById('tds_society_name').value = response.SocietyName_of_TDS;
		document.getElementById('show_in_email_bill_header').checked = (response.Show_Email_Postal_in_billheader == 1) ? true : false;	
		document.getElementById('apply_rounded_amt').checked = (response.IsRoundOffLedgerAmt == 1) ? true : false;	
		document.getElementById('reco_date_same_as_voucher').checked = (response.reco_date_same_as_voucher == 1) ? true : false;
		document.getElementById('virtual').checked = (response.show_vertual_ac == 1) ? true : false;
		document.getElementById('intercom').checked = (response.show_intercom == 1) ? true : false;
		document.getElementById("bill_temp").value= response.bill_template;
		document.getElementById('apply_Outstanding_amount').checked = (response.apply_Outstanding_amount == 1) ? true : false;
		document.getElementById('Authorised_Share_Capital_Text').value = response.Auth_Share_Capital_Text;
		document.getElementById('Authorised_Share_Capital_Amount').value = response.Auth_Share_Capital_Amount;
		document.getElementById('show_supp_reciept').checked = (response.show_reciept_on_supp == 1) ? true : false;
		document.getElementById("insert").value="Update";
		document.getElementById("insert1").value="Update";
		
		document.getElementById('bill_footer_view').style.display='none';
		document.getElementById('bill_footer_edit').style.display='block';
	}
	else if(arr1[0] == "show")	
	{
		
		document.getElementById("society_code").value=response.society_code;
		document.getElementById("society_name").value=response.society_name;
	    document.getElementById("circle").value=response.circle;
		document.getElementById("registration_date").value=response.registration_date;
		document.getElementById("registration_no").value=response.registration_no;
		document.getElementById("society_add").value=response.society_add;
		document.getElementById("city").value=response.city;
		document.getElementById("landmark").value=response.landmark;
		document.getElementById("state_id").value=response.state;
		document.getElementById("region").value=response.region;
		document.getElementById("postal_code").value=response.postal_code;	
		document.getElementById("phone").value=response.phone;
		document.getElementById("phone2").value=response.phone2;
		document.getElementById("fax_number").value=response.fax_number;
		document.getElementById("pan_no").value=response.pan_no;
		document.getElementById("tan_no").value=response.tan_no;
		document.getElementById("service_tax_no").value=response.service_tax_no;
		document.getElementById("email").value=response.email;
		document.getElementById("url").value=response.url;
		document.getElementById("member_since").value=response.member_since;
		document.getElementById("bill_cycle").value=response.bill_cycle;
		document.getElementById("int_rate").value=response.int_rate;
		document.getElementById("int_tri_amt").value=response.int_tri_amt;
		document.getElementById("int_method").value=response.int_method;
		document.getElementById("rebate_method").value=response.rebate_method;
		document.getElementById("rebate").value=response.rebate;
		document.getElementById("chq_bounce_charge").value=response.chq_bounce_charge;
		document.getElementById("bill_method").value=response.bill_method;
		document.getElementById("wing").checked= (response.show_wing == 1) ? true : false;
		document.getElementById("parking").checked= (response.show_parking == 1) ? true : false;
		document.getElementById("area").checked=(response.show_area == 1) ? true : false;
		document.getElementById("calc_int").checked=(response.calc_int == 1) ? true : false;
		document.getElementById("property_tax_no").value=response.property_tax_no;
		document.getElementById("water_tax_no").value=response.water_tax_no;
		document.getElementById("share").checked=(response.show_share == 1) ? true : false;
		document.getElementById('bill_footer').value = response.bill_footer;
		document.getElementById('bill_footer_view').innerHTML=response.bill_footer;
		document.getElementById('sms_start_text').value = response.sms_start_text;
		document.getElementById('sms_end_text').value = response.sms_end_text;
		document.getElementById('send_reminder').checked = (response.send_reminder_sms == 1) ? true : false;
		document.getElementById('bill_due_date').checked = (response.bill_due_date == 1) ? true : false;
		document.getElementById('show_floor').checked = (response.show_floor == 1) ? true : false;
		document.getElementById("unit_presentation").value=response.unit_presentation;
		document.getElementById("unit_presentation_previous_value").value=response.unit_presentation;
		document.getElementById('cc_email').value = response.cc_email;
		document.getElementById('bill_as_link').checked = (response.bill_as_link == 1) ? true : false;
		document.getElementById('email_contactno').value = response.email_contactno;
		document.getElementById('neft_notify_by_email').checked = (response.neft_notify_by_email == 1) ? true : false;
		document.getElementById('show_address_in_email').checked = (response.show_address_in_email == 1) ? true : false;
		document.getElementById('show_logo').checked = (response.show_logo == 1) ? true : false;	
		document.getElementById('show_QR_code').checked = (response.show_QR_code == 1) ? true : false;	
		document.getElementById('print_voucher_portrait').checked = (response.print_voucher_portrait == 1) ? true : false;	
		document.getElementById('apply_service_tax').checked = (response.apply_service_tax == 1) ? true : false;
		document.getElementById('service_tax_threshold').value = response.service_tax_threshold;
		//document.getElementById('service_tax_rate').value = arr2[51];
		document.getElementById('igst_tax_rate').value = response.igst_tax_rate;
		document.getElementById('cgst_tax_rate').value = response.cgst_tax_rate;
		document.getElementById('sgst_tax_rate').value = response.sgst_tax_rate;
		document.getElementById('cess_tax_rate').value = response.cess_tax_rate;
		document.getElementById('gstin_no').value = response.gstin_no;
		document.getElementById('apply_GST_On_Interest').checked = (response.apply_GST_On_Interest == 1) ? true : false;
		document.getElementById('apply_GST_above_Threshold').checked = (response.apply_GST_above_Threshold == 1) ? true : false;
		document.getElementById('gst_start_date').value=response.gst_start_date;
		document.getElementById('bank_penalty_amt').value = response.bank_penalty_amt;
		document.getElementById('notify_payment_voucher_daily_by_email').checked = (response.notify_payment_voucher_daily == 1) ? true : false;
		document.getElementById('notify_payment_voucher_daily_by_sms').checked = (response.notify_sms_payment_voucher_daily == 1) ? true : false;
		document.getElementById('balancesheet_temp').value=response.balancesheet_template;
		
		//Here We checking whether Send reminder is checked or not and according to that we are showing set reminder setup
		if(document.getElementById('send_reminder').checked == true)
		{
			document.getElementById('reminder_days').value = response.SMS_Reminder_Days;
		}
		else
		{
			document.getElementById('ReminderID').style.display = "none";
		}
		
		if(response.society_logo_thumb == '')
		{
			//document.getElementById('logomsg').style.display = 'none';
		}
		else
		{
			document.getElementById('logomsg').style.display = 'none';
			document.getElementById('logoImg').innerHTML = 'Logo Uploaded';
			document.getElementById('logobtn').style.display = 'none';
		}
		document.getElementById('apply_NEFT_member').checked = (response.Record_NEFT == 1) ? true : false;	
		document.getElementById('Send_reminder_email').checked = (response.send_reminder_email == 1) ? true : false;	
		document.getElementById('reminder_days_email').value = response.Email_Reminder_Days;
		var enable_Paytm = document.getElementById('enable_paytm').checked = (response.PaymentGateway == 1) ? true : false;
		
		if(enable_Paytm == true)
		{
			document.getElementById('pg_name_tr').style.display='table-row';
			document.getElementById('pg_link_tr').style.display='table-row';
			document.getElementById('payment_bank_tr').style.display='table-row';
			document.getElementById('pg_link').value = response.Paytm_Link;
			document.getElementById('pg_name').value = response.PGName;	
			document.getElementById('payment_bank').value = response.PGBeneficiaryBank;
			
		}
		else
		{
			document.getElementById('pg_name_tr').style.display='none';
			document.getElementById('pg_link_tr').style.display='none';
			document.getElementById('payment_bank_tr').style.display='none';
		}

		document.getElementById('tds_society_name').value = response.SocietyName_of_TDS;
		document.getElementById('show_in_email_bill_header').checked = (response.Show_Email_Postal_in_billheader == 1) ? true : false;	
		document.getElementById('apply_rounded_amt').checked = (response.IsRoundOffLedgerAmt == 1) ? true : false;	
		document.getElementById('reco_date_same_as_voucher').checked = (response.reco_date_same_as_voucher == 1) ? true : false;
		document.getElementById('virtual').checked = (response.show_vertual_ac == 1) ? true : false;
		document.getElementById('intercom').checked = (response.show_intercom == 1) ? true : false;
		document.getElementById("bill_temp").value= response.bill_template;
		document.getElementById('apply_Outstanding_amount').checked = (response.apply_Outstanding_amount == 1) ? true : false;
		document.getElementById('Authorised_Share_Capital_Text').value = response.Auth_Share_Capital_Text;
		document.getElementById('Authorised_Share_Capital_Amount').value = response.Auth_Share_Capital_Amount;
		if(response.society_QR_Code == '')
		{
			
		}
		else
		{
			document.getElementById('qrcodemsg').style.display = 'none';
			document.getElementById('qrcodeImg').innerHTML = 'QR Code Uploaded';
			document.getElementById('qrcodebtn').style.display = 'none';
		}
		document.getElementById('show_supp_reciept').checked = (response.show_reciept_on_supp == 1) ? true : false;
		var f = document.forms['society'];
		for(var i=0,fLen=f.length;i<fLen;i++)
		{
			$('#state_id').attr('disabled', true);
			$('#bill_cycle').attr('disabled', true);
			$('#int_method').attr('disabled', true);
			$('#rebate_method').attr('disabled', true);
			$('#bill_method').attr('disabled', true);
			$('#wing').attr('disabled', true);
			$('#area').attr('disabled', true);
			$('#parking').attr('disabled', true);
			$('#calc_int').attr('disabled', true);
			$('#share').attr('disabled', true);
			$('#send_reminder').attr('disabled', true);
			$('#bill_due_date').attr('disabled', true);
			$('#show_floor').attr('disabled', true);
			$('#unit_presentation').attr('disabled', true);
			$('#bill_as_link').attr('disabled', true);
			$('#neft_notify_by_email').attr('disabled', true);
			$('#notify_payment_voucher_daily_by_email').attr('disabled', true);
			$('#notify_payment_voucher_daily_by_sms').attr('disabled', true);
			$('#show_address_in_email').attr('disabled', true);
			$('#apply_service_tax').attr('disabled', true);
			$('#apply_GST_On_Interest').attr('disabled', true);
			$('#apply_GST_above_Threshold').attr('disabled', true);
			$('#apply_NEFT_member').attr('disabled', true);
			$('#Send_reminder_email').attr('disabled', true);
			$('#enable_paytm').attr('disabled', true);
			$('#show_in_email_bill_header').attr('disabled', true);
			$('#show_logo').attr('disabled', true);
			$('#show_QR_code').attr('disabled', true);
			$('#print_voucher_portrait').attr('disabled', true);
			$('#apply_rounded_amt').attr('disabled', true);
			$('#reco_date_same_as_voucher').attr('disabled', true);
			$('#virtual').attr('disabled', true);
			$('#intercom').attr('disabled', true);
			$('#bill_temp').attr('disabled', true);
			$('#apply_Outstanding_amount').attr('disabled', true);
			$('#Authorised_Share_Capital_Text').attr('disabled', true);
			$('#Authorised_Share_Capital_Amount').attr('disabled', true);
			$('#show_supp_reciept').attr('disabled', true);
			
		  f.elements[i].readOnly = true;//As @oldergod noted, the "O" must be upper case
		}
		//alert("apply field");
	
	$('.field_input').replaceWith(function(){
                    return '<span class='+this.className+'>'+this.value+'</span>'
                });
                $('.field_select').replaceWith(function(){
                    return '<span class='+this.className+'>' + this.options[this.selectedIndex].text + '</span>'
                });
                $('.field_date').replaceWith(function(){
                    return '<span class="">'+this.value+'</span>'
                });
				 $('.field_text').replaceWith(function(){
                    return '<span class='+this.className+'>'+this.value+'</span>'
                });
			//document.getElementById('bill_footer_view').style.display='block';
			document.getElementById('bill_footer_edit').style.display='none';
			document.getElementById("insert").value="Edit";
		document.getElementById("insert1").value="Edit";
	
	}
	else if(arr1[0] == "delete")
	{
		window.location.href ="../society.php?mst&"+arr1[1]+'&mm';
	}
	else
	{
		
	document.getElementById("society_code").readOnly='readOnly';	
	
	
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


function val()
{
	
	
	if(document.getElementById("insert").value == "Edit" || document.getElementById("insert1").value == "Edit")
	{
		return true;	
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	//var wing_id = trim(document.getElementById("wing_id").value);
	//var society_id=trim(document.getElementById("society_id").value);
	var society_code=trim(document.getElementById("society_code").value);
	var society_name = trim(document.getElementById("society_name").value);
	var circle = trim(document.getElementById("circle").value);
	var registration_date = trim(document.getElementById("registration_date").value);
	var registration_no = trim(document.getElementById("registration_no").value);
	var society_add = trim(document.getElementById("society_add").value);
	var landmark = trim(document.getElementById("landmark").value);
	var state = trim(document.getElementById("state_id").value);
	var city = trim(document.getElementById("city").value);
	var region = trim(document.getElementById("region").value);
	var postal_code = trim(document.getElementById("postal_code").value);
	var country = trim(document.getElementById("country").value);	
	var phone = trim(document.getElementById("phone").value);
	var fax = trim(document.getElementById('fax_number').value);
	var pan_no = trim(document.getElementById("pan_no").value);
	var tan_no = trim(document.getElementById("tan_no").value);
	var service_tax_no = trim(document.getElementById("service_tax_no").value);
	var email = trim(document.getElementById("email").value);
	var member_since = trim(document.getElementById("member_since").value);
	//var key = trim(document.getElementById("key").value);
	//var admin_user = trim(document.getElementById("admin_user").value);
	//var admin_pass = trim(document.getElementById("admin_pass").value);
	var billing_cycle = document.getElementById('bill_cycle').value;	
	var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
	document.getElementById("error").innerHTML = "Enter society address";
	var unit_presentation = document.getElementById('unit_presentation').value;
	var cc_email = trim(document.getElementById('cc_email').value);
	
		
	if(society_code=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter society code";
		document.getElementById("society_code").focus();
	//document.write(society_code);	
		go_error();
		return false;
	}
	

	//////////////////////////////////////////////////////////////////////////////////////////
	if(society_name=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter society name";
		document.getElementById("society_name").focus();
		
		go_error();
		return false;
	}
/////////////////////////////////////////////////////////////////////////////////////////////	
	/*if(circle=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter circle";
		document.getElementById("circle").focus();
		
		go_error();
		return false;
	}*/
	//////////////////////////////////////////////////////////////////////////////////////////
	/*if(society_add=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter society address";
		document.getElementById("society_add").focus();
		
		go_error();
		return false;
	}*/
	//////////////////////////////////////////////////////////////////////////////////////////
	/*if(landmark=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter landmark";
		document.getElementById("landmark").focus();
		
		go_error();
		return false;
	}*/
	//////////////////////////////////////////////////////////////////////////////////////////
	/*if(state=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Select state";
		document.getElementById("state_id").focus();
		
		go_error();
		return false;
	}*/
	//////////////////////////////////////////////////////////////////////////////////////////
	/*if(city=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter city";
		document.getElementById("city").focus();
		
		go_error();
		return false;
	}*/
	//////////////////////////////////////////////////////////////////////////////////////////
	/*if(region=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter region";
		document.getElementById("region").focus();
		
		go_error();
		return false;
	}*/
	//////////////////////////////////////////////////////////////////////////////////////////
	/*if(postal_code=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter postal code";
		document.getElementById("postal_code").focus();
		
		go_error();
		return false;
	}*/
	//////////////////////////////////////////////////////////////////////////////////////////
	/*if(country=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter country";
		document.getElementById("country").focus();
		
		go_error();
		return false;
	}*/
	//////////////////////////////////////////////////////////////////////////////////////////
	/*if(phone=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter phone";
		document.getElementById("phone").focus();
		
		go_error();
		return false;
	}*/
	//////////////////////////////////////////////////////////////////////////////////////////
	if(email=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter email id";
		document.getElementById("email").focus();
		
		go_error();
		return false;
	}
	else
	{
		if(reg.test(email) == false) 
		{
			document.getElementById('error').style.display = '';
			document.getElementById('error').innerHTML = 'Invalid email id';	
			document.getElementById('email').focus();
			
			go_error();
			return false;	
		}		
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	/*
	if(member_since=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Member since field should not be blank";
		document.getElementById("member_since").focus();
		
		go_error();
		return false;
	}
	*/
	//////////////////////////////////////////////////////////////////////////////////////////
	/*if(key=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Security No. should not be blank";
		document.getElementById("key").focus();
		
		go_error();
		return false;
	}*/
	/*if(admin_user=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Username should not be blank";
		document.getElementById("admin_user").focus();
		
		go_error();
		return false;
	}
	if(admin_pass=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Password should not be blank";
		document.getElementById("admin_pass").focus();
		
		go_error();
		return false;
	}*/


	if(billing_cycle == "" || billing_cycle == 0 )
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please Select Billing Cycle";
		go_error();
		return false;
	}	
	
	if(unit_presentation == "" || unit_presentation == 0 )
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please Select Unit Presentation";
		go_error();
		return false;
	}	
	
	if(cc_email != "")
	{
		if(reg.test(cc_email) == false) 
		{
			document.getElementById('error').style.display = '';
			document.getElementById('error').innerHTML = 'Invalid CC - email id';	
			document.getElementById('cc_email').focus();
			
			go_error();
			return false;	
		}		
	}
	
	//////////////////////////////////////////////////////////////////////////////////////////
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
	//////////////////////////////////////////////////////////////////////////////////////////
	
}
function UploadLogo()
{
	if(document.getElementById("photo").value == '')
	{
		alert("Please select Image");
		return false;
	}
	var societyId =document.getElementById("society_id").value;
	var images = document.getElementById("photo");
	var name = document.getElementById("photo").files[0].name;
	var society_code= document.getElementById("society_code").value;
	var ext = name.split('.').pop().toLowerCase();
	var form_data = new FormData();
  	if(jQuery.inArray(ext, ['png']) == -1) 
  	{
   		alert("Invalid Image File");
  	}
	
	 var reader = new FileReader();      
	 //Read the contents of Image File.
     reader.readAsDataURL(images.files[0]);
     reader.onload = function (e) {
     //Initiate the JavaScript Image object.
     var image = new Image();
    //Set the Base64 string return from FileReader as source.
     image.src = e.target.result;
     //Validate the File Height and Width.
     image.onload = function () {
     var height = this.height;
     var width = this.width;
     if (height != 181 && width != 210) {
        alert("Height and Width must be 210x181.");
        return false;
      }
	  else
	  {
		 form_data.append("file", document.getElementById('photo').files[0]);
		 form_data.append("method", "uploadsocietylogo");
		 form_data.append("society_code", society_code);
		 $.ajax({
			url: "ajax/ajaxsociety.php",
			type: "POST",
			data : form_data,//{"method" : 'uploadsocietylogo',"file": document.getElementById('photo').files[0]},
			contentType: false,
			cache: false,
			processData: false,
			success: function(data)
			{
				var arr1=new Array();
				
				arr1=data.split("@@@");
				console.log(arr1[0]);
				console.log(arr1[1]);
				if(arr1[1] > 0)
				{
					window.location.href = "society.php?id="+societyId+"&show&imp";	
					//window.location.href = '../society.php?id=<?php echo $_SESSION['society_id'];?>&show&imp';
				}
			},
			error: function(data)
			{
		  		console.log("error");
		  		console.log(data);
			}
   		}); 
	  }
    };
 }
}

function UploadQRCode()
{
	var societyId =document.getElementById("society_id").value;
	//alert(document.getElementById("qrcode").value);
	if(document.getElementById("qrcode").value == '')
	{
		alert("Please select QR Image");
		return false;
	}
	var images = document.getElementById("qrcode");
	var name = document.getElementById("qrcode").files[0].name;
	var society_code= document.getElementById("society_code").value;
	var ext = name.split('.').pop().toLowerCase();
	var form_data = new FormData();
  	if(jQuery.inArray(ext, ['png','jpg','jpeg']) == -1) 
  	{
   		alert("Invalid Image File");
  	}
	
	 var reader = new FileReader();      
	 //Read the contents of Image File.
     reader.readAsDataURL(images.files[0]);
     reader.onload = function (e) {
     //Initiate the JavaScript Image object.
     var image = new Image();
    //Set the Base64 string return from FileReader as source.
     image.src = e.target.result;
     //Validate the File Height and Width.
     image.onload = function () {
     var height = this.height;
     var width = this.width;
     if (height != 210 && width != 210) {
        alert("Height and Width must be 210x210.");
        return false;
      }
	  else
	  {
		 form_data.append("file", document.getElementById('qrcode').files[0]);
		 form_data.append("method", "uploadQRCode");
		 form_data.append("society_code", society_code);
		 $.ajax({
			url: "ajax/ajaxsociety.php",
			type: "POST",
			data : form_data,//{"method" : 'uploadsocietylogo',"file": document.getElementById('photo').files[0]},
			contentType: false,
			cache: false,
			processData: false,
			success: function(data)
			{
				
				var arr1=new Array();
				
				arr1=data.split("@@@");
				console.log(arr1[0]);
				console.log(arr1[1]);
				if(arr1[1] > 0)
				{
					window.location.href = "society.php?id="+societyId+"&show&imp";	
					//window.location.href = '../society.php?id=<?php echo $_SESSION['society_id'];?>&show&imp';
				}
				
			},
			error: function(data)
			{
		  		console.log("error");
		  		console.log(data);
			}
   		}); 
	  }
    };
 }
}
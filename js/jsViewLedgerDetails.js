var printMessage = "";
var idToSet = 0;
var prevDivContent = '';
var idToDelete = 0;
var sUrl = window.location.href;

function deleteVoucher(str)
{
	//alert(str);
	var iden	= new Array();
	iden		= str.split("-");

	if(iden[0]=="delete")
	{
		var conf = confirm("Are you sure , you want to delete it ???");
		if(conf==1)
		{
			idToDelete = iden[4];
			remoteCall("process/createvoucher.process.php","method="+iden[0]+"&Vno="+iden[1]+"&gid="+iden[2]+"&lid="+iden[3],"DeleteRecord");
		}
	}
	
}

function DeleteRecord()
{
	
	alert("Record Deleted Successfully....");
	location.reload(true);	
	//document.getElementById('tr_' + idToDelete).style.display = 'none';
	//idToDelete = 0;
}

function ViewVoucherDetail(ledgerID, groupID, voucherType, voucherID, allowShowLogBtn = false)
{
	idToSet = voucherID;
	
	var sURL = "ajax/view_ledger_details.ajax.php";
	var obj = {"lid" : ledgerID, "vid" : voucherID, "vtype" : voucherType, "getvoucherdetails" : "getvoucherdetails"};
	$("#show_log_btn").val(allowShowLogBtn);
	remoteCallNew(sURL, obj, 'detailsFetched');	
}

function detailsFetched()
{
	var sResponse = getResponse(RESPONSETYPE_STRING, true);
	var sMsg = sResponse;
	let showLogBtn = $("#show_log_btn").val();
	
	//window.location.href = sUrl + "#openDialogOk";
	
	var sText = '<a href="#close" title="Close" class="close" id="close" onClick="closeDialogBox();">X</a>';
	sText += '<center><font style="font-size:18px;"><b>Voucher Details</b></center></font>' + sMsg + '<br/>';
	sText += '<center><button name="Close" class="closeButton" id="dialogYesNo_yes"  onClick="closeDialogBox();">Close</button>';
	if(showLogBtn){

		sText += '<button name="changeLogBtn" class="changeLogBtn" id="changeLogBtn"  onClick="showChangeLog();">Show Log</button>';
	}
	sText += '<button name="cloneJV" class="cloneBtn" id="cloneJV"  onClick="createClone();">Clone</button></center>';
	
	sText += '<center><div class="logDiv"></div></center>';
	document.getElementById('message_ok').innerHTML = sText;
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

function showChangeLog(){

	let voucherDetails = $('#voucherDetail').val().split('-');

	let voucherTable = voucherDetails[0];

	let refNo = voucherDetails[1];

	if(voucherTable != '' && refNo != ''){

		window.open('showLog.php?vTable='+voucherTable+'&refNo='+refNo+'','type=fullWindow,fullscreen,scrollbars=yes');
	}
	
	return false;
}

function createClone(){

	let voucherDetails = $('#voucherDetail').val().split('-');

	let Vno = voucherDetails[1];

	if(Vno != ''){

		window.open('CloneVoucher.php?Vno='+Vno+'','type=fullWindow,fullscreen,scrollbars=yes');
	}
	
	return false;	
}



function ShowJV(lid)
{	
	window.open('createvoucher.php?lid='+lid,'popup','type=width=700,height=600,scrollbars=yes');
}

function ShowBankAccountDetails()
{
	window.open('BankAccountDetails.php','popup','type=width=700,height=600,scrollbars=yes');
}



function format(number) 
{
	number = String(number).replace(/,/g,'')
	if(number.length == 0)
	{
		number = 0;	
	}
	var bIsNegative = false;
	if(number < 0)
	{
		bIsNegative = true;
		number = Math.abs(number);
	}
	
    var decimalSeparator = ".";
    var thousandSeparator = ",";

    // make sure we have a string
    var result = String(number);

    // split the number in the integer and decimals, if any
    var parts = result.split(decimalSeparator);

    // if we don't have decimals, add .00
    if (!parts[1]) {
      parts[1] = "00";
    }
  
    // reverse the string (1719 becomes 9171)
    result = parts[0].split("").reverse().join("");

    // add thousand separator each 3 characters, except at the end of the string
    result = result.replace(/(\d{3}(?!$))/g, "$1" + thousandSeparator);

    // reverse back the integer and replace the original integer
    parts[0] = result.split("").reverse().join("");
	
    // recombine integer with decimals
    return  (bIsNegative == true)? ('-' + parts.join(decimalSeparator)) : (parts.join(decimalSeparator));
}

function delete_receipts()
{
	var coll = document.getElementsByName("check");
	var len = coll.length;
	//alert(len);
	var VoucherNos = new Array();
	var k = 0;
	for(var i = 0; i < len; i++)
	{
		if(coll[i].checked == true)
		{
			var id = coll[i].id;
			//alert(id);
			var VoucherNo = id.substring(6);
			//alert(VoucherID);
			VoucherNos[k] = VoucherNo;
			k++;
		}
	}
	/*for(var j = 0; j < VoucherIDs.length; j++)
	{
		alert(VoucherIDs[j]);
	}*/
	
	var obj = {"method" : "for_delete", "VoucherNos" : JSON.stringify(VoucherNos)};
	$.ajax({
	url : "ajax/view_ledger_details.ajax.php",
	type : "POST",
	data: obj,
	success : function(data)
	{
		alert("Entries deleted successfully.");
		location.reload(true);
		//remoteCall("ajax/ajaxPaymentDetails.php","form=PaymentDetails&method=delete&PaymentDetailsId="+arr2[1],"DeleteMultEntry");
	}
	});	
}

function link_vouchers(fd_id)
{
	var coll = document.getElementsByName("check");
	var len = coll.length;
	//alert(len);
	var VoucherNos = new Array();
	var k = 0;
	for(var i = 0; i < len; i++)
	{
		if(coll[i].checked == true)
		{
			var id = coll[i].id;
			//alert(id);
			var VoucherNo = id.substring(6);
			//alert(VoucherID);
			VoucherNos[k] = VoucherNo;
			k++;
		}
	}
	
	var obj = {"method" : "link_voucher", "fd_id" : fd_id, "VoucherNos" : JSON.stringify(VoucherNos)};
	$.ajax({
	url : "ajax/view_ledger_details.ajax.php",
	type : "POST",
	data: obj,
	success : function(data)
	{
		alert("Entries linked successfully.");
		location.reload(true);
		//remoteCall("ajax/ajaxPaymentDetails.php","form=PaymentDetails&method=delete&PaymentDetailsId="+arr2[1],"DeleteMultEntry");
	}
	});
}

function GetReport()
{
	var VoucherType = document.getElementById("voucherTypeID").value;
	var fromDate    = document.getElementById("from_date").value;
	var toDate      = document.getElementById("to_date").value;
	
	var obj = {"method" : "getReports", "voucherTypeID" : VoucherType, "fromDate" : fromDate, "toDate" : toDate};
	$.ajax({
	url : "ajax/view_ledger_details.ajax.php",
	type : "POST",
	data: obj,
	success : function(data)
	{
		document.getElementById("export_btn").style.display="block";
		document.getElementById('showTable').innerHTML = data;
	}
	});
}
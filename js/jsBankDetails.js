function getBankDetails(str)
{
	var iden	= new Array();
	iden		= str.split("-");

	//$('html, body').animate({ scrollTop: $('#top').offset().top }, 300);

	if(iden[0]=="delete")
	{
		var conf = confirm("Are you sure , you want to delete it ???");
		if(conf==1)
		{
			
			remoteCall("ajax/ajaxBankDetails.php","form=BankDetails&method="+iden[0]+"&BankDetailsId="+iden[1],"loadchanges");
		}
	}
	else
	{

		remoteCall("ajax/ajaxBankDetails.php","form=BankDetails&method="+iden[0]+"&BankDetailsId="+iden[1],"loadchanges");
	}
}

function loadchanges()
{
	var a		= sResponse.trim();
	var arr1	= new Array();
	var arr2	= new Array();
	arr1		= a.split("@@@");
	arr2		= JSON.parse("["+arr1[1]+"]");
	arr2 = arr2[0];

	if(arr1[0] == "edit")
	{
		document.getElementById('LedgerID').innerHTML = '<option>' + arr2.ledger_name.trim() + '</option>';
		document.getElementById('LedgerID').disabled = true;
		
		document.getElementById('BankName').value=arr2.BankName;
		document.getElementById('Bank_PreFix').value=arr2.BankPreFix;
		document.getElementById('BranchName').value=arr2.BranchName;
		
		//document.getElementById('LedgerName').value=arr2[3];
		//document.getElementById('LedgerName').disabled = true;
		
		document.getElementById('AcNumber').value=arr2.AcNumber;
		document.getElementById('Address').value=arr2.Address;
		document.getElementById('IFSC_Code').value=arr2.IFSC_Code;
		document.getElementById('MICR_Code').value=arr2.MICR_Code;
		document.getElementById('Phone1').value=arr2.Phone1
		document.getElementById('Phone2').value=arr2.Phone2;
		document.getElementById('Fax').value=arr2.Fax;
		document.getElementById('Email').value=arr2.Email;
		document.getElementById('Website').value=arr2.Website;
		document.getElementById('ContactPerson').value=arr2.ContactPerson;
		document.getElementById('ContactPersonPhone').value=arr2.ContactPersonPhone;
		document.getElementById('Note').value = arr2.Note;
				
		document.getElementById('Balance').value=arr2.opening_balance;
		//document.getElementById('Balance').disabled = true;
		
		document.getElementById('Balance_Date').value=arr2['Date'];
		//document.getElementById('Balance_Date').disabled = true;
				
		document.getElementById("id").value=arr2.BankID;
		document.getElementById("accountCategory").value=arr2['categoryid'];
		if(arr2.AllowNEFT == "1")
		{
			document.getElementById('AllowNEFT').checked = true;
		}
		else 
		{
			document.getElementById('AllowNEFT').checked = false;
		}
		document.getElementById("insert").value="Update";
	}
	else if(arr1[0] == "delete")
	{

		window.location.href ="../BankDetails.php?mst&"+arr1[1]+"&mm";
	}
}

function ledgerChange(ledger)
{
	if(ledger.value == 0)
	{
		document.getElementById('Balance').disabled = false;
		document.getElementById('Balance').value = 0;
		document.getElementById('Balance_Date').disabled = false;
		document.getElementById('Balance_Date').value = '';
		document.getElementById('Balance').value = '';
		document.getElementById('BankName').value = '';
		//document.getElementById('LedgerName').value = document.getElementById('BankName').value + ' ' + document.getElementById('BranchName').value;
	}
	else
	{
		//document.getElementById('Balance').disabled = true;
		//document.getElementById('Balance').value = '';
		//document.getElementById('Balance_Date').disabled = true;
		//document.getElementById('Balance_Date').value = '';
		//document.getElementById('LedgerName').disabled = true;
		document.getElementById('BankName').value = document.getElementById('LedgerID').options[document.getElementById('LedgerID').selectedIndex].text;;
		getOpeningBalanceAndDate();
	}
}

function getOpeningBalanceAndDate()
{
	document.getElementById('error').style.display = 'block';
	document.getElementById('error').innerHTML = 'Fetching Opening Balance...';
	var LedgerID = document.getElementById('LedgerID').value;
	
	var sURL = "ajax/ajaxBankDetails.php";
	var obj = {'getbalance':'', 'ledger': LedgerID };
	remoteCallNew(sURL, obj, 'balanceFetched');
}

function balanceFetched()
{
	document.getElementById('error').innerHTML = '';
	var sResponse = getResponse(RESPONSETYPE_STRING, true);
	aryResult = sResponse.split('@@@');
	if(aryResult[0] != null)
	{
		document.getElementById('Balance_Date').value = aryResult[0].trim();
	}
	if(aryResult[1] != null)
	{
		document.getElementById('Balance').value = aryResult[1].trim();
	}
	if(aryResult[2] != null)
	{
		document.getElementById('accountCategory').value = aryResult[2].trim();
	}
}
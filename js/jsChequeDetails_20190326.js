var delSetFlag = false;

function getChequeDetails(str)
{
	
	var iden	= new Array();
	iden		= str.split("-");

	//$('html, body').animate({ scrollTop: $('#top').offset().top }, 300);
//alert(iden);
	if(iden[0]=="delete")
	{
		delSetFlag = false;
		var conf = confirm("Are you sure , you want to delete it ?");
		if(conf==1)
		{
			$(document).ready(function()
			{
				$("#error").fadeIn("slow");
				//document.getElementById("error").innerHTML = "Please Wait...";
			});

			remoteCall("ajax/ajaxChequeDetails.php","form=ChequeDetails&method="+iden[0]+"&ChequeDetailsId="+iden[1],"DeleteRecord");
		}
	}
	else if(iden[0]=="print")
	{
		var chqId = iden[1];		
		$.ajax({
			url: 'ajax/ajaxChequeDetails.php',
        	type: 'POST',
        	data: {"chqId": chqId, "method":"FetchVoucher"},
        	success: function(data){
            fetchdata = data.split("@@@");
			VoucherData = fetchdata[1].split("#");					
			window.open('print_voucher.php?&vno=' + VoucherData[0] + '&type=' + VoucherData[1]);
			}
		})
	}
	else
	{
		$(document).ready(function()
		{
			$("#error").fadeIn("fast");
			document.getElementById("error").innerHTML = "Please Wait...";
			//$("#error").fadeOut("fast");
			//setTimeout('timeout("error")', 10);
		});
		remoteCall("ajax/ajaxChequeDetails.php","form=ChequeDetails&method="+iden[0]+"&ChequeDetailsId="+iden[1],"loadchanges");
	}
}

function DeleteMultEntry()
{	
	var out = document.getElementById('edit').value;
	alert("Record Deleted Successfully!!!");				
	if(out != "")
	{
		localStorage.setItem('refreshPage', "1");
		window.close();				
	}
	else
	{		
		location.reload(true);	
	}		
}

function DeleteRecord()
{
	var a		= sResponse.trim();
	var arr1	= new Array();
	var arr2	= new Array();
	arr1		= a.split("@@@");
	arr2 = arr1[1].split(",");	
	if(arr2.length-1 > 0)
	{		
		var obj = {"method" : "delete","PaymentDetailsId" : arr2[0]};
		$.ajax({
				url : "ajax/ajaxPaymentDetails.php",
				type : "POST",
				data: obj ,
				success : function(data)
				{	
					remoteCall("ajax/ajaxPaymentDetails.php","form=PaymentDetails&method=delete&PaymentDetailsId="+arr2[1],"DeleteMultEntry");
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
	else
	{		
		DeleteMultEntry();
	}		
}
 
function SubmitChequeDetails(minGlobalCurrentYearStartDate,maxGlobalCurrentYearEndDate)
{
	//alert('test');
	document.getElementById('Submit').disabled = true;
	var iRowCount = document.getElementById('maxrows').value;
	//alert(iRowCount);
	var VoucherDate = document.getElementById('VoucherDate').value;
	//alert(VoucherDate);
	var BankID = document.getElementById('BankID').value;
	//alert(BankID);
	var DepositID = document.getElementById('DepositID').value;
	//alert(DepositID);
	//return;
	var DateFlag = false;
	var VoucherDateFlag = false;
	var isReadyToSubmit = false;
	
	var arRowsToSubmit = [];
	var iSubmittedData = 0;
	var IsCallUpdtCnt = 1;
	var mode = 1;
	for(var iRows = 1 ; iRows < iRowCount; iRows++)
	{
		var str = 'PaidBy'+ iRows;
		//alert(str);
		var iPaidBy = document.getElementById('PaidBy'+ iRows).value;
		var BillType = document.getElementById('DrpDwnBillType' + iRows).value;
		var VoucherCounter = document.getElementById('VoucherCounter' + iRows).value;
					
		var	SystemVoucherNo = document.getElementById('Current_Counter'+iRows).value;
	/*	
		var CheckApplyCostCenter =document.getElementById('CostCenterApply').value;
		if(CheckApplyCostCenter == 1)
		{
		 CostCenter = document.getElementById('CostCenter' + iRows).value;
		}*/
		
		//alert("BillType :" + BillType);
		//alert(iPaidBy);
		
	
		if(iPaidBy.length > 0)
		{
			//alert(iSocietyID);
			
			//alert(iPaidBy);
			var iChequeNumber = 0;
			
			var Isduplicate = IsCounterDuplicate(VoucherCounter,mode,0);
			if(Isduplicate == false)
			{
				document.getElementById('Submit').disabled = false;
				return false;		
			}
			
			
			if(VoucherCounter != SystemVoucherNo)
			{
				IsCallUpdtCnt = UserResponseOnExCount(VoucherCounter,SystemVoucherNo);
			}
			
			if(DepositID != -3)
			{
				iChequeNumber = document.getElementById('ChequeNumber'+ iRows).value;
			//alert(iChequeNumber);
			}
			else
			{
				iChequeNumber = '-1';	
			}
			var ChequeDate = document.getElementById('ChequeDate'+ iRows).value;
			//alert(ChequeDate);
			var iAmount = document.getElementById('Amount'+ iRows).value;
			
			var iTDS_Amount = 0;
			if(TDSReceivable != 0) // IF TDS is set in default then only get value from input
			{
				iTDS_Amount = document.getElementById('TDS_Amount'+ iRows).value;				
			}

			var PayerBank = '';
			var PayerChequeBranch = '';
			if(DepositID != -3)
			{
				PayerBank = document.getElementById('PayerBank'+ iRows).value;
			//alert(PayerBank);
				PayerChequeBranch = document.getElementById('PayerChequeBranch'+ iRows).value;
			//alert(PayerChequeBranch);
			}
			else
			{
				PayerBank = '-';
				PayerChequeBranch = '-';	
			}
			var Comments = document.getElementById('Comments'+ iRows).value;
			//alert(Note);
			
			if(jsdateValidator('ChequeDate'+ iRows , ChequeDate , minGlobalCurrentYearStartDate,maxGlobalCurrentYearEndDate) == true)
			{
				//return true;
				DateFlag = true;	
			}
			else
			{
				DateFlag = false;		
			}
			
			
			
			if(VoucherDate.length > 0)
			{
				
				if(jsdateValidator('VoucherDate', VoucherDate ,minGlobalCurrentYearStartDate,maxGlobalCurrentYearEndDate) == true)
				{
					VoucherDateFlag = true;			
				}
				else
				{
					VoucherDateFlag = false;
				}
			}
			
			if(iChequeNumber == "" || ChequeDate == "" ||   iAmount == "" || PayerBank == "" || PayerChequeBranch == "" || DateFlag == false || VoucherDateFlag == false)
			{
				
				document.getElementById('Submit').disabled = false;
				var EmptyField = "";
				//alert(ChequeDate.length);
				if(iChequeNumber.length == 0)
				{
					if(EmptyField.length == 0)
					{
						EmptyField = "Cheque Number";
					}
					else
					{
						
						EmptyField += ", Cheque Number";
					}
				}
				if(ChequeDate.length == 0 || DateFlag == false)
				{
					if(EmptyField.length == 0)
					{
						EmptyField = "Cheque Date";
					}
					else
					{
						
						EmptyField += ", Cheque Date";
					}
				}
				if(iAmount.length == 0)
				{
					if(EmptyField.length == 0)
					{
						EmptyField = "Amount";
					}
					else
					{
						EmptyField += ", Amount";
					}
				}
				if(PayerBank.length == 0)
				{
					if(EmptyField.length == 0)
					{
						EmptyField = "Payer Bank";
					}
					else
					{
						EmptyField += ", Payer Bank";
					}
				}
				if(PayerChequeBranch.length == 0)
				{
					//alert(PayerChequeBranch);
					if(EmptyField.length == 0)
					{
						EmptyField = "Payer Cheque Branch";
					}
					else
					{
						
						EmptyField += ", Payer Cheque Branch";
					}
					//alert(EmptyField );
				}
				
				
				
							
				if(VoucherDateFlag == false)
				{
					if(EmptyField.length == 0)
					{
						EmptyField = "VoucherDate";
					}
					else
					{
						EmptyField += ",VoucherDate";
					}
					
				}
				
				
				document.getElementById('label'+ iRows).innerHTML = "Please enter valid value for " + EmptyField +".";
				document.getElementById('label'+ iRows).style.color = '#FF0000';
				//var strLabel = 'label'+ iRows;
				setTimeout('timeout(label'+ iRows +')', 5000);
				return;
				continue;
			}
			
			//alert(DepositID);
			//var sURL = "ajax/ajaxChequeDetails.php";
			var obj = {"PaidBy" : iPaidBy, "ChequeNumber" : iChequeNumber, "VoucherCounter" : VoucherCounter,"SystemVoucherNo":SystemVoucherNo, "IsCallUpdtCnt":IsCallUpdtCnt, "ChequeDate" : ChequeDate, "Amount" : iAmount, "PayerBank" : PayerBank, "PayerChequeBranch" : PayerChequeBranch, "VoucherDate" : VoucherDate, "BankID" : BankID, "DepositID" : DepositID,"Comments" : Comments,"BillType" :BillType,"TDS_Amount" : iTDS_Amount};
			//remoteCallNew(sURL, obj, 'dbUpdated');	
			
			arRowsToSubmit.push(obj);
			iSubmittedData++;
			isReadyToSubmit = true;
			
			document.getElementById('label'+ iRows).value = "Cheque Added";
			//document.getElementById(str).value = "";
			//if(iSocietyID != 0)
			//{
			//alert(iSocietyID);
			document.getElementById('PaidBy'+ iRows).value = '';
			//alert(iPaidBy);
			if(DepositID != -3)
			{
			document.getElementById('ChequeNumber'+ iRows).value = "";
			//alert(iChequeNumber);
			}
			document.getElementById('ChequeDate'+ iRows).value = "";
			//alert(ChequeDate);
			document.getElementById('Amount'+ iRows).value = "";
			if(TDSReceivable != 0)
			{
				document.getElementById('TDS_Amount'+ iRows).value = "";				
			}

			//alert(iAmount);
			if(DepositID != -3)
			{
			document.getElementById('PayerBank'+ iRows).value = "";
			//alert(PayerBank);
			document.getElementById('PayerChequeBranch'+ iRows).value = "";
			}			
			document.getElementById('Comments'+ iRows).value = "";
		}
	}
	
	if(isReadyToSubmit)
	{
		showLoader();	
		var objData = {'data' : JSON.stringify(arRowsToSubmit), "update" : 'update'}; 
		$.ajax({
				url : "ajax/ajaxChequeDetails.php",
				type : "POST",
				data: objData ,
				success : function(data)
				{	
					//if(LeafID == -1 && iSubmittedData > 0)
					//{
            alert("Record(s) entered successfully");
						location.reload(true);
					//}
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
}

function SubmitNeftDetails(minGlobalCurrentYearStartDate,maxGlobalCurrentYearEndDate)
{
	//alert('test');
	document.getElementById('Submit').disabled = true;
	var iRowCount = document.getElementById('maxrows').value;
	//alert(iRowCount);
	var VoucherDate = document.getElementById('VoucherDate').value;
	//alert(VoucherDate);
	var BankID = document.getElementById('BankID').value;
	
	
	//alert(BankID);
	var DepositID = -2;//document.getElementById('DepositID').value;
	//alert(DepositID);
	//return;
	
	var DateFlag = false;
	var VoucherDateFlag = false;
	var isReadyToSubmit = false;
	
	
	var arRowsToSubmit = [];
	var iSubmittedData = 0;
	
	var IsCallUpdtCnt = 1;
	var mode = 1;
	for(var iRows = 1 ; iRows < iRowCount; iRows++)
	{
		var str = 'PaidBy'+ iRows;
		//alert(str);
		var iPaidBy = document.getElementById('PaidBy'+ iRows).value;
		//alert(iPaidBy);
		if(iPaidBy.length > 0)
		{
			//alert(iSocietyID);
			
			//alert(iPaidBy);
			var iChequeNumber = document.getElementById('ChequeNumber'+ iRows).value;
			
			var iVoucherNumber = document.getElementById('VoucherCounter'+ iRows).value;
			
			//alert(iVoucherNumber);
			var ChequeDate = document.getElementById('ChequeDate'+ iRows).value;
			//alert(ChequeDate);
			var iAmount = document.getElementById('Amount'+ iRows).value;
			//alert(iAmount);
			var PayerBank = document.getElementById('PayerBank'+ iRows).value;
			//alert(PayerBank);
			var PayerChequeBranch = document.getElementById('PayerChequeBranch'+ iRows).value;
			//alert(PayerChequeBranch);
			var Comments = document.getElementById('Comments'+ iRows).value;
		    var BillType = document.getElementById('DrpDwnBillType' + iRows).value;
			//alert(Note);
			
			//var VoucherCounter = document.getElementById('VoucherCounter' + iRows).value;
						
			var	iSystemVoucherNo = document.getElementById('Current_Counter'+iRows).value;
		
			var Isduplicate = IsCounterDuplicate(iVoucherNumber,mode,0);
			
			if(Isduplicate == false)
			{
				document.getElementById('Submit').disabled = false;
				return false;		
			}
				
				
			if(iVoucherNumber != iSystemVoucherNo)
			{	
				IsCallUpdtCnt = UserResponseOnExCount(iVoucherNumber,iSystemVoucherNo);
			}
			
			if(jsdateValidator('ChequeDate'+ iRows , ChequeDate , minGlobalCurrentYearStartDate , maxGlobalCurrentYearEndDate) == true)
			{
				//return true;
				DateFlag = true;	
			}
			else
			{
				DateFlag = false;		
			}
			
			
			if(VoucherDate.length > 0)
			{
				
				if(jsdateValidator('VoucherDate', VoucherDate , minGlobalCurrentYearStartDate , maxGlobalCurrentYearEndDate) == true)
				{
					VoucherDateFlag = true;			
				}
				else
				{
					VoucherDateFlag = false;
				}
			}
			
			
			if(iChequeNumber == "" || ChequeDate == "" ||   iAmount == "" || PayerBank == "" || DateFlag == false || VoucherDateFlag == false)
			{
				document.getElementById('Submit').disabled = false;
				var EmptyField = "";
				//alert(ChequeDate.length);
				if(iChequeNumber.length == 0)
				{
					if(EmptyField.length == 0)
					{
						EmptyField = "Transaction Number";
					}
					else
					{
						
						EmptyField += ", Transaction Number";
					}
				}
				if(ChequeDate.length == 0 || DateFlag == false)
				{
					if(EmptyField.length == 0)
					{
						EmptyField = "Transaction Date";
					}
					else
					{
						
						EmptyField += ", Transaction Date";
					}
				}
				if(iAmount.length == 0)
				{
					if(EmptyField.length == 0)
					{
						EmptyField = "Amount";
					}
					else
					{
						EmptyField += ", Amount";
					}
				}
				if(PayerBank.length == 0)
				{
					if(EmptyField.length == 0)
					{
						EmptyField = "Payer Bank";
					}
					else
					{
						EmptyField += ", Payer Bank";
					}
				}
				
				if(VoucherDateFlag == false)
				{
					if(EmptyField.length == 0)
					{
						EmptyField = "VoucherDate";
					}
					else
					{
						EmptyField += ",VoucherDate";
					}
					
				}
				
				document.getElementById('label'+ iRows).innerHTML = "Please enter value for " + EmptyField +".";
				document.getElementById('label'+ iRows).style.color = '#FF0000';
				//var strLabel = 'label'+ iRows;
				setTimeout('timeout(label'+ iRows +')', 5000);
				return;
				continue;
			}
			
			//var sURL = "ajax/ajaxChequeDetails.php";
			var obj = {"PaidBy" : iPaidBy, "ChequeNumber" : iChequeNumber, "VoucherNumber": iVoucherNumber, "iSystemVoucherNo":iSystemVoucherNo,"IsCallUpdtCnt":IsCallUpdtCnt, "ChequeDate" : ChequeDate, "Amount" : iAmount, "PayerBank" : PayerBank, "PayerChequeBranch" : PayerChequeBranch, "VoucherDate" : VoucherDate, "BankID" : BankID, "DepositID" : -2,"Comments" : Comments,"BillType" : BillType};
			//remoteCallNew(sURL, obj, 'neftUpdated');	
			arRowsToSubmit.push(obj);
			iSubmittedData++;
			isReadyToSubmit = true;	
			document.getElementById('label'+ iRows).value = "Transaction Added";
			document.getElementById('PaidBy'+ iRows).value = '';
			document.getElementById('ChequeNumber'+ iRows).value = "";
			document.getElementById('VoucherCounter'+ iRows).value = "";
			document.getElementById('ChequeDate'+ iRows).value = "";
			document.getElementById('Amount'+ iRows).value = "";
			document.getElementById('PayerBank'+ iRows).value = "";
			document.getElementById('PayerChequeBranch'+ iRows).value = "";
			document.getElementById('Comments'+ iRows).value = "";
		}
	}
	
	if(isReadyToSubmit)
	{
		showLoader();	
		var objData = {'data' : JSON.stringify(arRowsToSubmit), "updateneft" : 'updateneft'}; 
		$.ajax({
				url : "ajax/ajaxChequeDetails.php",
				type : "POST",
				data: objData ,
				success : function(data)
				{	
					//if(LeafID == -1 && iSubmittedData > 0)
					//{
					location.reload(true);
					//}
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
}

function dbUpdated()
{
	alert('Data Added Successfully');
}

function neftUpdated()
{
	alert('Data Added Successfully');
}



function loadchanges()
{	
	var a		= sResponse.trim();
	var arr1	= new Array();
	var arr2	= new Array();
	arr1		= a.split("@@@");
	arr2		= JSON.parse("["+arr1[1]+"]");
	
	console.log(arr2);

//alert(arr2[0]);
	if(arr1[0] == "edit")
	{
		$(document).ready(function()
		{
			$("#error").fadeIn("slow");
			//document.getElementById("error").innerHTML = "Please Wait...";
		});
		
		
		if(arr2[0].Show_JvFormat == 1)
		{
			window.open("VoucherEdit.php?Vno="+arr2[0].VoucherNo+"&bankid="+arr2[0].BankID+"&receipt="+1);
			return
			
		}
		
			//alert(arr2);
		var DepositID=document.getElementById('DepositID').value=arr2[0].DepositID;
		//alert(DepositID);
		var maxRowCounter=document.getElementById('maxrows').value;
		
		for(var row=2;row < maxRowCounter;row++)
		{
			document.getElementById('PaidBy' + row).style.display = 'none';
			if(DepositID != -3)
			{
			document.getElementById('ChequeNumber' + row).style.display = 'none';
			document.getElementById('PayerBank' + row).style.display = 'none';
			document.getElementById('PayerChequeBranch' + row).style.display = 'none';
			}
			document.getElementById('ChequeDate' + row).style.display = 'none';
			document.getElementById('Amount' + row).style.display = 'none';
			
			document.getElementById('Comments' + row).style.display = 'none';
			if(arr2[0].DepositID != '-2')
			{
				document.getElementById('ChqRow' + row).style.display = 'none';
				document.getElementById('ChqRowLabel' + row).style.display = 'none';  
			}
			else
			{
				document.getElementById('NeftRow' + row).style.display = 'none';
				document.getElementById('NeftRowLabel' + row).style.display = 'none';
				
			}
		}
		console.log("Array Value "+arr2);
		//alert("in loadchanges");	
		document.getElementById('VoucherDate').value=arr2[0].VoucherDate;
		if(DepositID != -3)
		{
			document.getElementById('ChequeNumber1').value=arr2[0].ChequeNumber;
			document.getElementById('PayerBank1').value=arr2[0].PayerBank;
			document.getElementById('PayerChequeBranch1').value=arr2[0].PayerChequeBranch;
		}
		document.getElementById('VoucherCounter1').value=arr2[0].VoucherNumber;
		document.getElementById('OnPageLoadTimeVoucherNumber1').value=arr2[0].VoucherNumber;
		
		document.getElementById('ChequeDate1').value=arr2[0].ChequeDate;
		document.getElementById('Amount1').value=arr2[0].Amount;
		document.getElementById('Amount1').focus();
		document.getElementById('PaidBy1').value=arr2[0].PaidBy;
		
		if(TDSReceivable != 0) //If TDS Receivable set in default then only it will set the value
		{
			document.getElementById('TDS_Amount1').value=arr2[0].TDS_Amount;			
		}

		document.getElementById('BankID').value=arr2[0].BankID;
		
		document.getElementById('Comments1').value=arr2[0].Comments;
		//alert("in loadchanges1");
		if(arr2[0].DepositID != '-2')
			{
		document.getElementById("ChequeDetailid").value=arr2[0].ID; 
		document.getElementById('MembersOnly').checked= false;
			}
		document.getElementById('Submit').value="Update";
		document.getElementById('addnewrow').style.display = 'none';
		if(arr2[0].DepositID != '-2')
		{		
		document.getElementById('Submit').onclick=function(){ UpdateChequeDetails(arr2[0].ID,minGlobalCurrentYearStartDate,maxGlobalCurrentYearEndDate)};
		}
		else
		{
			document.getElementById('DepositID').value=-2;
			document.getElementById('Submit').onclick=function(){ UpdateChequeDetails(arr2[0].ID,minGlobalCurrentYearStartDate,maxGlobalCurrentYearEndDate)};
		}
		
		document.getElementById('DrpDwnBillType1').value = arr2[0].BillType;
		if(arr2[0].IsMemberLedger == 1)
		{
			document.getElementById("MembersOnly").checked = true;
		}
		else
		{
			document.getElementById("MembersOnly").checked = false;
		}
		
		toggleSupplementaryCheckbox(1);
		//checking reconcile status
		if(arr2[0].ReconcileStatus == 1)
		{
			document.getElementById('ChequeDate1').style.backgroundColor = 'lightgray';
			document.getElementById('ChequeDate1').disabled = true;
			
			document.getElementById('ChequeNumber1').style.backgroundColor = 'lightgray';
			document.getElementById('ChequeNumber1').disabled = true;
			
			document.getElementById('Amount1').style.backgroundColor = 'lightgray';			
			document.getElementById('Amount1').disabled = true;
			
			document.getElementById('DrpDwnBillType1').disabled = true;
		}
		//Checking return cheque entry
		if(arr2[0].Return == 1)
		{
			document.getElementById('PaidBy1').style.backgroundColor = 'lightgray';
			document.getElementById('PaidBy1').disabled = true;
		}
		else
		{
			document.getElementById('PaidBy1').style.backgroundColor = 'white';
			document.getElementById('PaidBy1').disabled = false;
		}
		
		document.getElementById("error").innerHTML = " ";
		
		if(delSetFlag)
		{
			//alert(arr2[0]);
			getChequeDetails('delete-' + arr2[0].ID);
		}
		
	}
	else if(arr1[0] == "delete")
	{
		$(document).ready(function()
		{
			$("#error").fadeIn("slow");
			document.getElementById("error").innerHTML = "Please Wait...";
		});

		window.location.href ="../ChequeDetails.php?mst&"+arr1[1]+"&mm";
	}
}

function UpdateChequeDetails(ChequeDetailsId,minGlobalCurrentYearStartDate,maxGlobalCurrentYearEndDate)
{
 //alert("UpdateChequeDetails");
 //alert(ChequeDetailsId);	
	 document.getElementById('Submit').disabled = true;
 	var VoucherDate = document.getElementById('VoucherDate').value;
	//alert(VoucherDate);
	var BankID = document.getElementById('BankID').value;
	//alert(BankID);
	var DepositID = document.getElementById('DepositID').value;
	//alert(DepositID);
	//return;
	
	var arRowsToSubmit = [];
	//var str = 'PaidBy'+ iRows;
		//alert(str);
	var iPaidBy = document.getElementById('PaidBy1').value;
	var BillType = document.getElementById('DrpDwnBillType1').value;
	//alert("BillType" + BillType);
	//alert(iPaidBy);
	var iChequeNumber = 0;
	var DateFlag = false;
	var VoucherDateFlag = false;
	var IsCallUpdtCnt = 1;
	var mode = 3;
	if(DepositID != -3)
	{
		iChequeNumber = document.getElementById('ChequeNumber1').value;
	//alert(iChequeNumber);
	}
	else
	{
		iChequeNumber = '-1';	
	}
	var ChequeDate = document.getElementById('ChequeDate1').value;
	
	var VoucherCounter = document.getElementById('VoucherCounter1').value;	
	
	var CVoucherNumber = document.getElementById('Current_Counter1').value;	
	//alert(ChequeDate);
	var iAmount = document.getElementById('Amount1').value;
	
	var iTDS_Amount = 0;
	
	if(TDSReceivable != 0) // if TDS Receivable is set in default then only fetch value
	{
		iTDS_Amount = document.getElementById('TDS_Amount1').value;
	}	
	var PayerBank = '';
	var PayerChequeBranch = '';
	var OnPageLoadTimeVoucherNumber = document.getElementById('OnPageLoadTimeVoucherNumber1').value;
	
	var Isduplicate = IsCounterDuplicate(VoucherCounter,mode,OnPageLoadTimeVoucherNumber);
	
	if(Isduplicate == false)
	{
		document.getElementById('Submit').disabled = false;
		return false;		
	}
	
	
	if(VoucherCounter != CVoucherNumber)
	{
		IsCallUpdtCnt = UserResponseOnExCount(VoucherCounter,CVoucherNumber);
	}
	
	if(DepositID != -3)
	{
		PayerBank = document.getElementById('PayerBank1').value;
	//alert(PayerBank);
		PayerChequeBranch = document.getElementById('PayerChequeBranch1').value;
	//alert(PayerChequeBranch);
	}
	else
	{
		PayerBank = '-';
		PayerChequeBranch = '-';	
	}
	var Comments = document.getElementById('Comments1').value;
	//alert(Note);
	
	
	if(jsdateValidator('ChequeDate1', ChequeDate , minGlobalCurrentYearStartDate , maxGlobalCurrentYearEndDate) == true)
	{
		//return true;
		DateFlag = true;	
	}
	else
	{
		DateFlag = false;		
	}
	if(VoucherDate.length > 0)
	{
		
		if(jsdateValidator('VoucherDate', VoucherDate , minGlobalCurrentYearStartDate , maxGlobalCurrentYearEndDate) == true)
		{
			VoucherDateFlag = true;			
		}
		else
		{
			VoucherDateFlag = false;
		}
	}
	
	if(iChequeNumber == "" || ChequeDate == "" ||   iAmount == "" || PayerBank == "" || PayerChequeBranch == "" || iPaidBy.length <= 0 || DateFlag == false ||  VoucherDateFlag == false)
	{
		document.getElementById('Submit').disabled = false;
		var EmptyField = "";
		//alert(ChequeDate.length);
		
		if(iPaidBy.length <= 0)
		{
			if(EmptyField.length == 0)
			{
				EmptyField = "PaidBy";
			}
			else
			{
				
				EmptyField += ", PaidBy";
			}
		}
		
		if(iChequeNumber.length == 0)
		{
			if(EmptyField.length == 0)
			{
				EmptyField = "Cheque Number";
			}
			else
			{
				
				EmptyField += ", Cheque Number";
			}
		}
		if(ChequeDate.length == 0 || DateFlag == false)
		{
			if(EmptyField.length == 0)
			{
				EmptyField = "Cheque Date";
			}
			else
			{
				
				EmptyField += ", Cheque Date";
			}
		}
		if(iAmount.length == 0)
		{
			if(EmptyField.length == 0)
			{
				EmptyField = "Amount";
			}
			else
			{
				EmptyField += ", Amount";
			}
		}
		if(PayerBank.length == 0)
		{
			if(EmptyField.length == 0)
			{
				EmptyField = "Payer Bank";
			}
			else
			{
				EmptyField += ", Payer Bank";
			}
		}
		if(PayerChequeBranch.length == 0)
		{
			//alert(PayerChequeBranch);
			if(EmptyField.length == 0)
			{
				EmptyField = "Payer Cheque Branch";
			}
			else
			{
				
				EmptyField += ", Payer Cheque Branch";
			}
			//alert(EmptyField );
		}
		
		
		if(VoucherDateFlag == false)
		{
			if(EmptyField.length == 0)
			{
				EmptyField = "VoucherDate";
			}
			else
			{
				EmptyField += ",VoucherDate";
			}
			
		}
		document.getElementById('label1').innerHTML = "Please enter value for " + EmptyField +".";
		document.getElementById('label1').style.color = '#FF0000';
		//var strLabel = 'label'+ iRows;
		setTimeout('timeout(label1)', 5000);
		
	}
	else
	{
	var obj = {"PaidBy" : iPaidBy, "ChequeNumber" : iChequeNumber, "VoucherCounter":VoucherCounter, "OnPageLoadTimeVoucherNumber": OnPageLoadTimeVoucherNumber,"IsCallUpdtCnt":IsCallUpdtCnt, "ChequeDate" : ChequeDate, "Amount" : iAmount, "PayerBank" : PayerBank, "PayerChequeBranch" : PayerChequeBranch, "VoucherDate" : VoucherDate, "BankID" : BankID, "DepositID" : DepositID,"Comments" : Comments,"BillType" : BillType,"TDS_Amount" : iTDS_Amount};
	//remoteCallNew(sURL, obj, 'dbUpdated');	
	
	arRowsToSubmit.push(obj);
	//iSubmittedData++;
	
	document.getElementById('label1').value = "Cheque Added";
	document.getElementById('PaidBy1').value = '';
	if(DepositID != -3)
	{
	document.getElementById('ChequeNumber1').value = "";
	//alert(iChequeNumber);
	}
	document.getElementById('ChequeDate1').value = "";
	document.getElementById('Amount1').value = "";
	
	if(TDSReceivable != 0) 
	{
		document.getElementById('TDS_Amount1').value = "";
	} 
	if(DepositID != -3)
	{
	document.getElementById('PayerBank1').value = "";
	document.getElementById('PayerChequeBranch1').value = "";
	}			
	document.getElementById('Comments1').value = "";

var objData = {'data' : JSON.stringify(arRowsToSubmit), "method" : 'updateChequeDetails',"ChequeDetailsId" : ChequeDetailsId }; 
showLoader();	
$.ajax({
	url : "ajax/ajaxChequeDetails.php",
	type : "POST",
	data: objData ,
	success : function(data)
	{	
		alert("Record Updated Successfully!!!");	
		var out = document.getElementById('edit').value;
					
		if(out != "")
		{
			localStorage.setItem('refreshPage', "1");
			window.close();				
		}
		else
		{
			location.reload(true);
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
}


var PayerBankID = "";
var PayerBankCount = "";
var PayerBankNBranch = [];

function PayerBankFetched()
{
	//*** If PayerBankNBranch empty then execute
	hide_error();
	if(PayerBankNBranch == "")
	{
		var sResponse = getResponse(RESPONSETYPE_STRING, true);	
		var aryBanks = [];
		aryBanks = sResponse.split("@@@");
	 	PayerBankNBranch.push(aryBanks[1]);
	 	PayerBankNBranch = JSON.parse("["+PayerBankNBranch+"]");	
		AutoPopBankDetails();
	}
}
	
function AutoPopBankDetails()
{
	for(var i = 0; i < PayerBankNBranch.length; i++) 
		{
	 		for(var j=0;j<PayerBankNBranch[i].length;j++)
	 			{
					if(PayerBankNBranch[i][j].unit_id == PayerBankID)
						{
							if(document.getElementById('DepositID').value != -3) // If it is not cash ledger then set PayerBank and Payer Cheque Branch
							{
								document.getElementById('PayerBank' + PayerBankCount).value = PayerBankNBranch[i][j].Payer_Bank;
								document.getElementById('PayerChequeBranch' + PayerBankCount).value = PayerBankNBranch[i][j].Payer_Cheque_Branch;
							}
							document.getElementById('LedgerBalance' + PayerBankCount).innerHTML  = "Ledger Balance : "+PayerBankNBranch[i][j].LedgerBalance;
						}
	 			}
		}
}	
		
function PopulatePayerBank(PaidBy, Counter)
	{
		//** On Tab Off it check PayerBankNBrach array is not empty if yes then it fetch data from database and store inside the array esle it call AutoPopBankDetails
		 
		PayerBankID = PaidBy;
		PayerBankCount = Counter;
		var sURL = "ajax/ajaxChequeDetails.php";
		var obj = {'getBankDetails':'', 'PaidBy':PaidBy, 'Counter':Counter};
		if(PayerBankNBranch == "")
		{
			remoteCallNew(sURL, obj, 'PayerBankFetched');
		}
		else if(PayerBankNBranch != "")
		{
			AutoPopBankDetails();
		}
	}
	
function timeout(field)
{
	//alert(field.id);
	document.getElementById(field.id).innerHTML = "";
	
}



function toggleSupplementaryCheckbox(iCount)
{
	var chkoptions = document.getElementsByClassName('billCheckbox'), c;
	if(document.getElementById("MembersOnly").checked != 1)
	{
		/*for (c = 0; c < chkoptions.length;c += 1)
		{
			chkoptions[c].checked = false;
			chkoptions[c].disabled = true;
		}*/
		//document.getElementById("DrpDwnBillType" + iCount).checked =false;
		//document.getElementById("DrpDwnBillType" + iCount).disabled = true;
		
	}
	else
	{
		/*for (c = 0; c < chkoptions.length;c += 1)
		{
			chkoptions[c].disabled = false;
		}*/
		document.getElementById("DrpDwnBillType" + iCount).disabled = false;
	}
}
// JavaScript Document
var bIsSendEmail = false;
function jsBillUpdate(HeaderAndAmount,NewRowCounter)
{
	var obj = [];
	var CurrentValue =0;
	var PreviousValue =0;
	var arDataToSubmit = [];
	var HeaderID="";
	var invoiceNumber = "";
	var CheckSocietyApplyGST = "";
	var IsInvoiceEdit = 0;
	var EditableInvoiceNo = 0;
	var ExitingInvoiceUnitID = 0;
	var VoucherNumber = 0;
	var IsCallUpdtCnt = 1;
	var mode = 3;
	
	//Request type Will define where bill is updating or created
	
	var RequestType = document.getElementById('request_type').value;
	
	// Here Checking the Society is GST Appy or not
	
	if(RequestType =='Invoice')
	{
		CheckSocietyApplyGST = document.getElementById('SocietyTaxable').value;
	}
	for(var i=0; i < HeaderAndAmount.length;i++)
	{
		arr23 = HeaderAndAmount[i].split("@@@");
		HeaderID = arr23[1].toString();
		if(document.getElementById("txtInterestOnArrears").value != HeaderID)
		{ 
			VoucherID = arr23[0].toString();
			Taxable = arr23[2].toString();
			Taxable_no_threshold = arr23[3].toString();
			//alert(Taxable_no_threshold);
			CurrentValue = document.getElementById(arr23[0]).value;
			PreviousValue = document.getElementById(arr23[0]).defaultValue;
			obj = {'Head' : HeaderID , 'Amt' : CurrentValue , 'HeadOldValue' : PreviousValue, 'VoucherID' : VoucherID, 'Taxable' : Taxable, 'Taxable_no_threshold' : Taxable_no_threshold};
			arDataToSubmit.push(obj);
		}
	}
	
	  if(NewRowCounter != 0)
	  {
		 arDataToSubmit = ReturnNewRowCounter(NewRowCounter,arDataToSubmit,RequestType,0);
	  }
	

	for (var key in obj) 
				{
					//alert(obj[key]);
				}
			
	var IsSuppBill = document.getElementById('IsSupplementaryBill').value;
	//if(IsSuppBill == 0)
	{	
			if(RequestType == 'Invoice')
				{
				// We checking here whether is it on edit mode to create
				var InvoiceMode = document.getElementById('IsInvoiceEdit').value
				var IsOutSider = document.getElementById('IsOutSider').value;	
				var InterestOnPrincipleDue = 0;
				var IntrestOnPreviousarrears = 0;
	    		var PrinciplePreviousArrears = 0;
				var AdjustmentCredit = 0;
				var Note  = CKEDITOR.instances['note'].getData();
				//var Note = document.getElementById('note').value;
				//console.log(Note);
				
				var bill_date = document.getElementById('bill_date').value;
				invoiceNumber =  document.getElementById('invoiceNumber').value;
				
				if(InvoiceMode == 1)
				{
					//setting the value for edit mode
					IsInvoiceEdit = 1;
					IsCallUpdtCnt = 0;
					
					VoucherNumber = document.getElementById('bill_no').value;
					EditableInvoiceNo = document.getElementById('EditInvoiceNo').value;
					ExitingInvoiceUnitID = document.getElementById('ExitingInvoiceUnitID').value;
					
					var isDuplicateCounter =  IsCounterDuplicate(VoucherNumber,mode,EditableInvoiceNo);
					
					if(isDuplicateCounter == false)
					{
						return false;
					}
					
					if(EditableInvoiceNo != VoucherNumber)
					{
						IsCallUpdtCnt = UserResponseOnExCount(VoucherNumber,invoiceNumber);
					}		
				}
				
				if(IsOutSider == 0)
				{
					var UnitID = document.getElementById('UnitID').value;
				}
				else if(IsOutSider == 1)
				{
				   var UnitID = document.getElementById('Outsider').value;
				}
				
 				}
			else
				{
				var InterestOnPrincipleDue = document.getElementById('InterestOnPrincipleDue').value;
				var IntrestOnPreviousarrears = document.getElementById('IntrestOnPreviousarrears').value;
	    		var PrinciplePreviousArrears = document.getElementById('PrinciplePreviousArrears').value;
				var AdjustmentCredit = document.getElementById('AdjustmentCredit').value;;
				var Note = 0; 
				CheckSocietyApplyGST = 0;
				var bill_date = 0;
				var UnitID = document.getElementById('UnitID').value;
				}
					
	    		var PeriodID = document.getElementById('PeriodID').value;
				
				
				//Now we Validating Sale Invoice Data
				
				if(RequestType=='Invoice')
				{
					//This just for identify whether it is unit or outsider select box
					if(IsOutSider == 1)
					{
						var OutSiderCombo = document.getElementById('Outsider').value;
						if(OutSiderCombo == "" || OutSiderCombo == 0)
						{
						alert('Please Select Ledger which you want to create invoice');
						return false;
						}
					}
					if(IsOutSider == 0)
					{
						if((UnitID=="" || UnitID==0) || (bill_date=="" || bill_date==0))
							{
								alert("Please check  UnitID & bill date field is not empty");
								return false;
							}
					}
				
					
					if(InvoiceMode == 1)
					{
						//During the edit mode if for any particular unit has already  voucher number and next bill for same unit voucher numer is edited to same as previuos unit voucher number then problem occur to prevent that below code is written.
						var isVoucherAlreadyExits = CheckSameVoucherNumberNotAssingSameUnit(VoucherNumber,EditableInvoiceNo);
						if(isVoucherAlreadyExits == true)
						{
								alert("Please change the voucher Number because same voucher number already register to this unit");
								return false;		
						}
					}
				}	
		var objData = {'data' : JSON.stringify(arDataToSubmit), "method" : 'BillEdit',"RequestType":RequestType, "Note": Note,"UnitID" : UnitID,"bill_date":bill_date,"PeriodID" : PeriodID,"InterestOnPrincipleDue" : InterestOnPrincipleDue,"IntrestOnPreviousarrears" : IntrestOnPreviousarrears,"PrinciplePreviousArrears" : PrinciplePreviousArrears,"AdjustmentCredit":AdjustmentCredit,"SupplementaryBill" : IsSuppBill, "IsInvoiceEdit":IsInvoiceEdit, "EditableInvoiceNo":EditableInvoiceNo, "ExitingInvoiceUnitID":ExitingInvoiceUnitID, "IsCallUpdtCnt":IsCallUpdtCnt,"VoucherCounter":VoucherNumber};
		
		$.ajax({
				url : "ajax/ajaxgenbill.php",
				type : "POST",
				data: objData ,
				success : function(data)
				{	
					if(RequestType == 'edt')
					{
					alert('Bill Updated Successfully');
					location.reload(true);
					window.location.href = "Maintenance_bill.php?UnitID="+ UnitID + '&PeriodID='+ PeriodID+ '&BT='+IsSuppBill;
					}
					else if(RequestType == 'Invoice')
					{	
						if(IsInvoiceEdit == 1)
						{		
						alert("Invoice bill Updated Successfully");
						location.reload(true);
						window.location.href = "Invoice.php?UnitID="+ UnitID+'&inv_number='+VoucherNumber;
						}
						else
						{
							alert("Invoice bill created");
							location.reload(true);
							window.location.href = "Invoice.php?UnitID="+ UnitID+'&inv_number='+invoiceNumber;
						}		
					}
					else if(IsInvoiceEdit == 1)
					{		
					}
					else if(data='emptyfield')
					{
						alert("Please fill all the required field");
					}
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

function CheckSameVoucherNumberNotAssingSameUnit(voucherNumber,EditableInovoiceNo)
{
	//In ExtingVoucher we get all exiting voucher for requested unit
	var ExitingVouchers = document.getElementById('VoucherArray').value;
	ExitingVouchers = JSON.parse("["+ExitingVouchers+"]");
	console.log(voucherNumber);
	console.log(ExitingVouchers);
	if(EditableInovoiceNo == voucherNumber)
	{
		return false;
	}
	else
	{
		for(var i = 0; i < ExitingVouchers.length ; i++)
		{
			for(var j=0;j<ExitingVouchers[i].length;j++)
			{
				console.log(j);
				console.log(ExitingVouchers[i][j].Inv_Number);
				console.log(voucherNumber);
				if(ExitingVouchers[i][j].Inv_Number == parseInt(voucherNumber))
				{
					return true;
				}
			}
			return false;
		}	
	}	
}


function AddCreditDebitNote(NewRowCounter,NoteType,RequestType,IsEditModeSet)
{
	var arDataToSubmit = [];
	var editableCreditDebitId = 0;
	var IsCallUpdtCnt = 1;
	var mode = 3;
	var UnitID = document.getElementById('UnitID').value;
	var VoucherNumber = 0;
	var EditableInvoiceNo = 0;
	var NextVoucherNumber = 0;
	
	if(IsEditModeSet == 1)
	{
		editableCreditDebitId = document.getElementById('editableId').value;
		VoucherNumber = document.getElementById('bill_no').value;
		EditableCreditDebitNo = document.getElementById('EditInvoiceNo').value;
		NextVoucherNumber = document.getElementById('invoiceNumber').value;
	}
	var DebitCreditTableID  = document.getElementById('DebitCreditID').value;
	var bill_date = document.getElementById('bill_date').value;
	var BillType = $("input[name=BillType]:checked").val();
	//var Note = document.getElementById('note').value;
	var Note  = CKEDITOR.instances['note'].getData();
	
	var Head = document.getElementById('particular1').value;
	var Amount = document.getElementById('HeaderAmount1').value;
	//Validation Part 
	
	if(UnitID == '' || UnitID == 0)
	{
		alert("Please select the member");
		return false;
	}
	
	if(Head == '' || Head == 0 || Amount == "" || Amount == 0)
	{
		if(Head == '' || Head == 0)
		{
			alert("Please select  the Ledger");
			return false;
		}
		if(Amount == "" || Amount == 0)
		{
			alert("Please insert the amount");
			return false;
		}	
	}
	if(IsEditModeSet == 1)
	{
		var isDuplicateCounter =  IsCounterDuplicate(VoucherNumber,mode,EditableCreditDebitNo);
					
		if(isDuplicateCounter == false)
		{
			return false;
		}
	}
	
	//Calling ReturnNewRowCounter to return data in array format
	arDataToSubmit = ReturnNewRowCounter(NewRowCounter,arDataToSubmit,RequestType,NoteType);
	
	var objData = {'data' : JSON.stringify(arDataToSubmit), "method" : 'AddCreditDebitNote',"UnitID" : UnitID,"bill_date":bill_date,"BillType":BillType,"NoteType":NoteType, "Note": Note,"IsEditModeSet":IsEditModeSet,"editableCreditDebitId":editableCreditDebitId,"IsCallUpdtCnt":IsCallUpdtCnt,"VoucherNumber":VoucherNumber};
		
	$.ajax({
			url : "ajax/ajaxgenbill.php",
			type : "POST",
			data: objData ,
			success : function(data)
			{	
				if(NoteType == document.getElementById('CreditNoteType').value)
				{
					var NoteName = 'Credit';
				}
				else
				{
					var NoteName = 'Debit';
				}
				
				if(IsEditModeSet == true)
				{
					alert(NoteName +' Note Updated Successfully');
					location.reload(true);
					window.location.href = "Invoice.php?debitcredit_id="+editableCreditDebitId+"&UnitID="+UnitID+"&NoteType="+NoteType;					
				}
				else
				{
					alert(NoteName +' Note Created Successfully');
					location.reload(true);
					window.location.href = "Invoice.php?debitcredit_id="+DebitCreditTableID+"&UnitID="+UnitID+"&NoteType="+NoteType;
				}

			}
		});
}

function ReturnNewRowCounter(NewRowCounter,arDataToSubmit,RequestType,NoteType)
{
	CheckSocietyApplyGST = false;
	if(RequestType == 'Invoice')
	{
		CheckSocietyApplyGST = document.getElementById('SocietyTaxable').value;		
	}

	if(NewRowCounter >0)
	{
		var invoiceTaxable="";
		for(var m=1;m <= NewRowCounter; m++)
		{
			HeaderID = document.getElementById('particular'+m).value;
			CurrentValue = document.getElementById('HeaderAmount'+m).value;
			//Here We are  setting value of ledger whether is taxable of not
			if(CheckSocietyApplyGST == 1 && RequestType == 'Invoice')
			{
				LedgerTaxable = document.getElementById('invoicetaxable'+m).checked;
				if(LedgerTaxable == true)
					{
						LedgerTaxable = 1;
					}
				else
					{
						LedgerTaxable = 0;
					}	
			}
			else
			{
				LedgerTaxable = 0;
			}
			PreviousValue=0;
			VoucherID=0
			if(HeaderID !=0 && CurrentValue !=0)
			{
				if(RequestType == 'Invoice')
				{
					obj = {'Head' : HeaderID ,'Amt' : CurrentValue ,'HeadOldValue' : PreviousValue,'VoucherID' : VoucherID,'invoicetaxable':LedgerTaxable};
				}
				else
				{
					obj = {'Head' : HeaderID ,'Amt' : CurrentValue ,'HeadOldValue' : PreviousValue,'VoucherID' : VoucherID};
				}

				arDataToSubmit.push(obj);
			}
		}
	}
	return arDataToSubmit;
}

function SundryDebtorCheckbox()
{
	//Here We showing and hidding the Bill To Drop down in invoice 
	var SundryDebtorCheckbox = document.getElementById('OutSideServices').checked;
	if(SundryDebtorCheckbox == false)
	{
		//This will show sale checked ledger
		document.getElementById('IsOutSider').value = '0';
		document.getElementById('Outside').style.display = "none";
		document.getElementById('Unit').style.display = "table-row";
	}
	else if(SundryDebtorCheckbox == true)
	{
		//This will all member 
		document.getElementById('IsOutSider').value = '1';
		document.getElementById('Outside').style.display = "table-row";
		document.getElementById('Unit').style.display = "none"; 
	}
}

//This Function Auto check the Checkbox in sale invoice if ledger is taxable or not

function IsTaxable(NewRowCounter)
{
	var Count = 0;
	  for(var m=1;m <= NewRowCounter; m++)
		{	
			var	showtax=document.getElementById('particular'+m).value;
			Count++;
		}
			$.ajax({
				url : "ajax/ajaxgenbill.php",
				type : "POST",
				data: {"method" : 'Checktaxable',"showtax":showtax},
				success : function(data)
				{
				document.getElementById('invoicetaxable'+Count).checked = ( data == 1 ) ? true : false ;
				}
		})	
}

function SubmitBillRows()
{
	alert("inside SubmitBillRows");
}


function sendEmail(unitID,periodID,bGeneratePdf,sEmail,billType,Isinvoice)
{
	var Invoice_Number = "";
	showLoader();
	document.getElementById('send_email').disabled = true;
	document.getElementById('status').style.visibility = 'visible';
	document.getElementById('status').innerHTML = 'Sending ...';
	//alert("unitID"+unitID+"periodID"+periodID+"bGeneratePdf"+bGeneratePdf+"sEmail"+sEmail+"billType"+billType);
	//return false;

	if(billType == null)
	{
		console.log("No bill type as been set.");
		billType = 0;
	}
	//generating pdf for maintenance_bill
	if(bGeneratePdf == true)
	{
		bIsSendEmail = true;
		ViewPDF(unitID,periodID);
	}
	else
	{
		//it check whether sale invoice bill or not and set the default value for period and invoice number
		if(Isinvoice == true)
		{
			Invoice_Number = periodID;
			periodID = 0;
		}
		bIsSendEmail = false;
		SentEmailManually = 1;
		$.ajax(
		{
				url : "classes/email.class.php",
				type : "POST",
				data: { "unit":unitID, "period":periodID, "Invoice_Number" :Invoice_Number, "SentEmailManually":SentEmailManually, "BT" : billType} ,
				success : function(data)
				{	
					var unitsAry = JSON.parse(data);
					for(var objUnit in unitsAry)
					{
						if(unitsAry[objUnit].trim() == "Success")
						{
							data = "Email sent successfully to :" + " " +sEmail;	
						}
						else
						{
							data = "" + unitsAry[objUnit];	
						}
						
					}
					document.getElementById('status').innerHTML = data;
					document.getElementById('send_email').disabled = false;
					hideLoader();
				},
					
				fail: function()
				{
					document.getElementById('send_email').disabled = false;
					hideLoader();
				},
				
				error: function(XMLHttpRequest, textStatus, errorThrown) 
				{
					document.getElementById('send_email').disabled = false;
					hideLoader();
				}
		}
		);
	}
}

//Now we setup the page on edit mode
function GetInvoiceDetails(LedgerDetails,UserDetails)
{
	CheckSocietyApplyGST = document.getElementById('SocietyTaxable').value;
	var IsInvoiceEdit = document.getElementById('IsInvoiceEdit').value
	
	
	var RowCnt = 1;
	for(var i = 0; i < LedgerDetails.length; i++)
	{
		//Calling the AddNewRow() to add new row and setting the details in the new row
		AddNewRow();
		document.getElementById('particular'+RowCnt).value = LedgerDetails[i].LedgerID;
		if(CheckSocietyApplyGST == 1)
		{
			document.getElementById('invoicetaxable'+RowCnt).checked = (LedgerDetails[i].Taxable != 0)? true : false ;	
		}
		document.getElementById('HeaderAmount'+RowCnt).value = LedgerDetails[i].LedgerAmt ;
		RowCnt++;
	}
	document.getElementById('note').value = UserDetails.Note;
	
	if(IsInvoiceEdit == true)
	{
		//IF SundryDebtorCheckbox true then checked the SundryDebtor Checkbox
		document.getElementById('OutSideServices').checked = (UserDetails.SundryDebtorsChkbox == true)? true : false ;
		if(UserDetails.SundryDebtorsChkbox == true)
		{
		SundryDebtorCheckbox();
		document.getElementById('Outsider').value = UserDetails.UnitID;	
		}
		else
		{
			document.getElementById('UnitID').value = UserDetails.UnitID;
		}
	}
	else
	{
		document.getElementById('UnitID').value = UserDetails.UnitID;
	}
}


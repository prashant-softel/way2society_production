function ValidateData(minStartDate,maxEndDate)
{
	
	
	var iRowCount = document.getElementById('maxrows').value;
	var VoucherDate = document.getElementById('voucher_date').value;
	var Comments = document.getElementById('Note').value;
	var BankID = document.getElementById("BankID").value;
	if(BankID == "0")
	{
		var isCheck=document.getElementById('is_invoice').value;
		var Invoice=document.getElementById('invoice_no').value;		
	}

	var VoucherNumber = document.getElementById('VoucherNumber').value;
	var CurrentNumber = document.getElementById('Current_Counter').value; 
	var OnPageLoadTimeVoucherNumber = document.getElementById('OnPageLoadTimeVoucherNumber').value; 
	var mode = document.getElementById('mode').value; 
	var tableName = document.getElementById('Updatetable').value;
	var DateString = "Voucher";
	var Isduplicate = IsCounterDuplicate(VoucherNumber,mode,OnPageLoadTimeVoucherNumber);
	//alert(Isduplicate+'  is Duplicate '+VoucherNumber+'VoucherNumber');
	
	if(tableName == 3) //3 is constant value of TABLE_PAYMENT_DETAILS in php
	{
		DateString = "Cheque";
		var ChequeNumber = document.getElementById("ChequeNumber").value;
		
		if(ChequeNumber == "")
		{
			document.getElementById('error').innerHTML = "Please Enter Cheque Number ..";
			document.getElementById('error').style.color = '#FF0000';
			setTimeout('timeout(error)', 3000);
			return false;
		}
		
		var IsBankLedgerPresent = false;
	
		for(var i = 1 ; i <= iRowCount ; i++) 
		{
			var LedgerID = document.getElementById('To'+i).value;
			if(BankLedgers.indexOf(LedgerID) != -1) // If bank ledger found in drop down then set IsBankLedgerPresent to true
			{
				IsBankLedgerPresent = true;
			}
		}
		
		if(IsBankLedgerPresent == false) // Checking whether user selected bank or cash ledger
		{
			document.getElementById('error').innerHTML = "Please Select Bank or Cash Ledger ..";
			return false;	
		}
	}

	
	if(Isduplicate == false)
	{
		return false;		
	}
	
	if(VoucherNumber != CurrentNumber)
	{
		document.getElementById('IsCallUpdtCnt').value = UserResponseOnExCount(VoucherNumber,CurrentNumber);
	}	
	
	if(Comments=="" || VoucherDate == "")
	{			
				if(Comments=="")
				{
				document.getElementById('Note').value = "Please Enter Note For  Your Reference.";
				document.getElementById('Note').style.color = '#FF0000';
				setTimeout('timeout(Note)', 3000);	
				
				}
				if(VoucherDate=="")
				{
					document.getElementById('error').innerHTML = "Please Enter "+DateString+" Date..";
					document.getElementById('error').style.color = '#FF0000';
					setTimeout('timeout(error)', 6000);
				}
				
				
		return false;		
	}
	if(isCheck==1 && Invoice=="")
				{
					//alert("test");
				document.getElementById('error').innerHTML = "Please Enter Invoice Number..";
				document.getElementById('error').style.color = '#FF0000';
				setTimeout('timeout(error)', 6000);
				return false;
					
				}
	
	if(VoucherDate.length > 0)
	{
		if(jsdateValidator('voucher_date',VoucherDate,minStartDate,maxEndDate) == true)
		{
			//return true;	
		}
		else
		{
			return false;		
		}
		
	}
	var EmptyField = "";
	for(var iRows = 1 ; iRows <= iRowCount; iRows++)
	{
		
			
			var ByTo = document.getElementById('byto'+ iRows).value;
			//alert(ByTo);
			var LedgerName = document.getElementById('To'+ iRows).value;
			var DebitAmount = document.getElementById('Debit' +iRows).value;
			var CreditAmount = document.getElementById('Credit'+ iRows).value;
			//alert(CreditAmount);
			
			//alert(Comments);
			if(ByTo=='BY')
			{
					//alert(ByTo);
				
				if(LedgerName == '0' || DebitAmount == "" )
				{
					EmptyField = "";	
								if( LedgerName == '0')
								  {
									  //alert("ledgername");
										if(EmptyField.length == 0)
										{
											//alert("1");
											EmptyField = "Ledger Name";
										}
										else
										{
											//alert("2");
											EmptyField += ", Ledger Name";
										}
								   }
								
								if(DebitAmount.length == 0)
								{
									//alert("debitamt");
										if(EmptyField.length == 0)
										{
											//alert("1");
											EmptyField = "Debit";
										}
										else
										{
											//alert("2");
											EmptyField += ", Debit";
										}
								
								}
								 document.getElementById('label'+ iRows).innerHTML = "Please enter value for " + EmptyField +".";
								 document.getElementById('label'+ iRows).style.color = '#FF0000';
				                 setTimeout('timeout(label'+ iRows +')', 6000);
								 
								 return false;
								
				
					
				  }
				 /*else
				  {
					  return true;
					  
				  }*/
				 
				 
			}
			else
			{
				//alert(ByTo);
					EmptyField = "";
					if((LedgerName == '0' && CreditAmount != "") || (LedgerName != '0' && CreditAmount == ""))
					{
						//alert("0/0/false");
						
						   if( LedgerName == '0')
						   {
								  //alert("LedgerName");
									if(EmptyField.length == 0)
									{
										//alert("1");
										EmptyField = "Ledger Name";
									}
									else
									{
										//alert("2");
										EmptyField += ", Ledger Name";
									}
							}
							
							if(CreditAmount.length == 0)
							{
								//alert("CreditAmount");
								
									if(EmptyField.length == 0)
									{
										//alert("1");
										EmptyField = "Credit";
									}
									else
									{
										//alert("2");
										EmptyField += ", Credit";
									}
							}
					
							document.getElementById('label'+ iRows).innerHTML = "Please enter value for " + EmptyField +".";
							document.getElementById('label'+ iRows).style.color = '#FF0000';
							setTimeout('timeout(label'+ iRows +')', 6000);	
							return false;
					}
			}
		
		        
				
	}
	
return true;
	
}

function validateTotal(Credit,Debit)
{
	//alert("validateTotal");
	if(Credit==Debit)
	{
		//alert("Credit==Debit");	
		return true;
	}
	else
	{
		//alert("Credit not equal Debit");
	return false;
	}
}

function timeout(field)
{
	document.getElementById(field.id).innerHTML = "";
}



function checkForEmptyRowAlreadyExists(rowCounter)
{
	
	for(var iRows = 1 ; iRows <= rowCounter; iRows++)
	{
		var ByTo = document.getElementById('byto'+ iRows).value;
		var CreditAmount = document.getElementById('Credit'+ iRows).value;
		
		if(ByTo=='TO')
		{
			if(CreditAmount == "" && CreditAmount.length == 0 )
			{
				return false;
			}
		}
	}
	
	return true;
}
function doFormSubmit(minStartDate,maxEndDate)
{
	  if(AddValues(false) == true)
	  {
	  		return ValidateData(minStartDate,maxEndDate);
	  }
	  else
	  {
		  alert("Sum of Credit  Amounts Does Not Match With Sum of  Debit Amounts "); 
		  return false;  
	}
}

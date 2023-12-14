
var aryExCounter = [];

function getExCounter(CurrentVcounter)
{
	var isCntPresent = aryExCounter.indexOf(CurrentVcounter);
	
	if(isCntPresent != -1)
	{
		while(aryExCounter.indexOf(CurrentVcounter) != -1)
		{
			CurrentVcounter++;
		}
		return CurrentVcounter;
	} 
	else
	{
		return CurrentVcounter;
	}
}

function IsCounterDuplicate(voucherNumber,mode,OnPageLoadTimeVoucherNumber)
{
	/*In this function 
	VoucherNumber is Voucher Number enter by user
	mode can be 1 which is add mode or 3 which is edit mode
	OnPageLoadTimeVoucherNumber this param only used on edit mode
	*/
	//Voucher Number is in string for indexof function it should must be number 
	voucherNumber = parseInt(voucherNumber);
	console.log('Voucher Number'+voucherNumber);
	console.log('Page Load '+OnPageLoadTimeVoucherNumber);
	var isCntPresent = aryExCounter.indexOf(voucherNumber);	
	
	// Here check whether value find in array or not if found the cose execute further
	if(isCntPresent != -1)
	{
		if(mode == 1)
		{
			var userRepose = confirm('Voucher number '+voucherNumber+' already exits \nDo you want to continue.');
			if(userRepose == true)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else if(mode == 3)
		{
				if(voucherNumber != OnPageLoadTimeVoucherNumber)
				{
					var userRepose = confirm('Voucher number '+voucherNumber+' already exits \nDo you want to continue.');
					if(userRepose == true)
					{
						return true;
					}
					else
					{
						return false;
					}
				}
				else
				{
					// here OnPageLoadTimeVoucherNumber and VoucherNumber both matches . Here alredy voucherNumber is present so if entry more than 1 then only it duplicate
					var presentNo_Time = 0;
					for(var i = 0 ; i < aryExCounter.length; i++)
					{
						if(aryExCounter[i] == voucherNumber)
						{
							presentNo_Time++;
						}
					}
					if(presentNo_Time > 1)
					{
						var userRepose = confirm('Voucher number '+voucherNumber+' already exits \nDo you want to continue.');
						if(userRepose == true)
						{
							return true;
						}
						else
						{
							return false;
						}
					}
			}		
		}
	}
	else
	{
		return true;
	}
	
}

function SetAry_ExitingExCounter(ExVoucherary)
{
   		aryExCounter = JSON.parse("["+ExVoucherary+"]");		
}

function UserResponseOnExCount(VoucherNumber,CVoucherNumber)
{
	if(parseInt(VoucherNumber) > parseInt(CVoucherNumber))
		{
			var Response = confirm("Next Voucher Number is "+CVoucherNumber+" and you enter the "+VoucherNumber+". \nDo you want to update the counter of default page with "+VoucherNumber);
			
			if(Response == true)
			{
				return IsCallUpdtCnt = 1;
			}
			else
			{
				return IsCallUpdtCnt = 0;
			}
		}
}

function deleteDebitorCredit(DebitorCreditID,NoteType)
{
	var NoteName = "Credit";
	if(NoteType == 2)
	{
		NoteName = "Debit";
	}
	var IsDebitorCreditDelete = confirm("Are you sure you want to delete ? ");
    if( IsDebitorCreditDelete == true )
	{
		//This code will execute when above condition will true and delete process begin 
		
     	$.ajax({
		url : "ajax/ajaxgenbill.php",
		type : "POST",
		data: {"method": "deleteDebitorCredit", "DebitorCreditID":DebitorCreditID,"NoteType":NoteType},
		success: function(data)
		{
			alert(NoteName+" Note deleted successfully");
			window.location.reload();
		}
		});
    }
    else
	{
		//If user cancel the delete process so it will terminate the delete proces 
		 
	    return false;
    }
}

function deleteInvoice(InvoiceNumber,UnitID,billdate)
{
	//First Take Confirmation from user whether  they want to delete or not ?
	
	var IsInvoiceDelete = confirm("Are you sure you want to delete Invoice ? ");
    if( IsInvoiceDelete == true )
	{
		//This code will execute when above condition will true and delete process begin 
		alert("Bill Date"+billdate);
     	$.ajax({
		url : "ajax/ajaxgenbill.php",
		type : "POST",
		data: {"method": "deleteInvoice", "InvoiceNumber":InvoiceNumber, "UnitID":UnitID,"billdate":billdate},
		success: function(data)
		{
			alert("Your Invoice deleted successfully");
			//window.location.reload();
		}
		});
    }
    else
	{
		//If user cancel the delete process so it will terminate the delete proces 
	    return false;
    }
            
}
//  Freez Year validations
function FreezYear(val)
{
	
	$.ajax({
				url: 'ajax/defaults.ajax.php',
        		type: 'POST',
        		data: {"YearID": val, "method":"GetYearData"},
        		success: function(data)
				{	
					var res=JSON.parse(data); 
					if(res[0]['value'] == 1)
					{
						
							//var r = confirm("We observe that financial year "+res[0]['YearDescription']+" is not locked. If the audit is completed we recomment to lock the year, So that financial entries can not be change.\nClick ok to freez the financial year.");
  							//if (r == true) 
							//{
								//window.open("bill_year.php","_blank");
   							//} 
							//else 
							//{
    							
 							//}
							document.getElementById("freezYearMsg").innerHTML = "";
					}
					else if(res[0]['value'] == 2)
					{
							document.getElementById("freezYearMsg").innerHTML = "Financial Year " +res[0]['YearDescription'] +" is locked, So that financial entries can not be change.";
							//$.("#freezYearMsg").html("Year Already lock");
					}
					else
					{
						document.getElementById("freezYearMsg").innerHTML = "";
					}
					
				}
				
		  });
}




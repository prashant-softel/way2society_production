var delSetFlag = false;
var newArr	= new Array();		
var isCreatedNewLedger = false;
var ExpenseByArr = new Array();
var aryChqNo = [];
var leafID;
var customleaf = 0;
var i = 1;
var counterArray = [];
//aryExCounter array used to store ExternalVoucherCounter;
// JavaScript Document
function view(id,counter)
{		
	var comment = document.getElementById('Comment'+counter).value;
		if(comment != '')
		{
			return false;
		}
	$.ajax({
		url : "ajax/ajaxPaymentDetails.php",
		type : "POST",
		datatype: "JSON",
		data : {"method":"Fetch","lId":id},
		success : function(data)
		{	
		var fetchdata = data.split("@@@");
		document.getElementById('Comment'+counter).value = fetchdata[1];
		
			//document.getElementById("mem_id").innerHTML=data;
		}
	});
}


function GetChequeDetails(ChequeNumber, iCounter, LeafID)
{ 
	$.ajax({
			url : "ajax/ajaxPaymentDetails.php",
			type : "POST",
			data: { "ChequeNumber":ChequeNumber, "LeafID":LeafID, "mode":"Fill"} ,
			success : function(data)
			{	
				//alert(data[0]['Amount']);
				var a		= data.trim();
				var arr1	= new Array();
				var arr2	= new Array();
				arr1		= a.split("@@@");
			
				if(arr1[1].length > 0)
				{
					arr2 = arr1[1].split("#");
					document.getElementById('PaidTo'+iCounter).value=arr2[0];
					document.getElementById('PaidTo'+iCounter).style.backgroundColor = 'lightgray';
					document.getElementById('PaidTo'+iCounter).disabled = true;
					document.getElementById('DE' + iCounter).checked=false;
					if(arr2[1] != 0)
					{
						document.getElementById('ExpenseTo'+iCounter).disabled = false;
						//document.getElementById('ExpenseTo'+iCounter).color = '#FF0000';
						document.getElementById('ExpenseTo'+iCounter).value=arr2[1];
						document.getElementById('ExpenseTo'+iCounter).disabled = true;
						document.getElementById('DE' + iCounter).checked=true;
					}
					
					document.getElementById('DE' + iCounter).disabled=true;
					document.getElementById('ExpenseTo'+iCounter).style.backgroundColor = 'lightgray';
					
					document.getElementById('ChequeDate'+iCounter).value=arr2[2];
					document.getElementById('ChequeDate'+iCounter).style.backgroundColor = 'lightgray';
					
					document.getElementById('Amount'+iCounter).value=arr2[3];
					document.getElementById('Amount'+iCounter).style.backgroundColor = 'lightgray';
					
					document.getElementById('Comment'+iCounter).value=arr2[4];
					document.getElementById('Comment'+iCounter).style.backgroundColor = 'lightgray';
					
					document.getElementById('ChequeNumber'+iCounter).value=ChequeNumber;
					document.getElementById('ChequeNumber'+iCounter).style.backgroundColor = 'lightgray';
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


function getExistingData(chqAry, LeafNo, bIsCustomLeaf)
{	 
	var isAnyChequeFill = false;
	if(bIsCustomLeaf == null)
	{
		bIsCustomLeaf = "0";
	}
	$.ajax({
			url : "ajax/ajaxPaymentDetails.php",
			type : "POST",
			data: { "Cheque": JSON.stringify(chqAry), "LeafID":LeafNo, "mode":"Fill","CustomLeaf": bIsCustomLeaf} ,
			success : function(data)
			{					
				var arr1	= new Array();
				var arr2	= new Array();
				var arr3	= new Array();
				var arr4	= new Array();
				var arr5 	= new Array();				
				arr1		= data.split("@@@");
				
				arr2 = arr1[1].split('^^');
				
				for(var i = 1 ; i < arr2.length ; i++)
				{					
					arr3 = arr2[i].split('@@');	
																																	
					if(arr3[1].length > 0)							
					{												
						isAnyChequeFill = true;						 
						var iCount = arr3[0];												
						var iData = arr3[1];												
						arr5 = iData.split('_//_');												
						//for(var c = 1; i < arr5.length; i++)
						{							
							//arr4 = arr5[1].split('#');	
							arr4 = JSON.parse("["+arr5[1]+"]");	
							//console.log(arr4);					
							
							if(arr2.length == 2 && LeafNo == -1)
							{									
								document.getElementById('VoucherDate').value= arr4[0].VoucherDate;	
							}	
							//console.log("Value  "+arr4[0])					
							document.getElementById('PaidTo'+iCount).value= arr4[0].PaidTo;
							document.getElementById('PaidTo'+iCount).style.backgroundColor = 'lightgray';
							document.getElementById('PaidTo'+iCount).disabled = true;
							//document.getElementById('DE' + iCount).checked=false;
							var groupID=document.getElementById('GroupID'+iCount).value = arr4[0].group_id;
							
							//alert(arr4[14]);
							/*if(groupID == 4)
							{
								document.getElementById('DE'+iCount).disable=true;
								document.getElementById('DE'+iCount).style.backgroundColor = 'lightgray';
								var	table='<table width="100%" style="margin-top: 5px;">';
								table+="<tr align='center'>"
								table +="<th style='width:15%;text-align: center; color:red;'>You have derectly booking to Expense. if you have already booked for invoice expense click to edit button please uncheck to expense invoice. </th>";
								table+="</tr></table>";	
								document.getElementById('ShowMesssgedata'+ iCount).innerHTML=table;
								
								
							}*/
							
							if(arr4[0].InvoiceData != null && arr4[0].InvoiceData != "")
							{
								var arr6	= new Array();
								var arr6=JSON.parse(arr4[0].InvoiceData);
								//console.log(arr6);
								var	table='<table width="118%" style="margin-top: -3px;">';
								
								var InvoiceTotalAmount=0;
								var TDSTotalAmount=0;
								var dues=0;
								var TotalDues=0;
								var ClearVoucherNo=0;
								table+="<tr style='background-color: #d9edf7; height:22px;    line-height: 20px;' align='center'>"
									table +="<th style='width:15%;text-align: center;'>Invoice date</th>";
									table +="<th style='width:35%;text-align: center;'>Expense To</th>";
									table +="<th style='width:15%;text-align: center;'>Invoice Amount</th>";
									table +="<th style='width:15%;text-align: center;'>TDS Amount</th>";
									table +="<th style='width:30%;text-align: center;'>Comments</th>";
									table+="</tr>"
								for(var d=0;d < arr6.length;d++)
								{   
									ClearVoucherNo=arr6[d]['ExpenseDetails']['InvoiceClearedVoucherNo'];
									//alert(ClearVoucherNo);
									table+="<tr>"
									table +="<td style='width:100px;text-align: center;'><b>"+arr6[d]['ExpenseDetails']['date']+"</b></td>";
									table +="<td style='width:200px ;text-align: center;'><b>"+arr6[d]['ExpenseDetails']['ledger_name']+"</b></td>";
									table +="<td style='width:100px ;font-weight: bold;text-align: center;' id='InvoiceValue'>"+arr6[d]['InvoceAmount']+"</td>";
									table +="<td style='width:100px;font-weight: bold;text-align: center;'  id='TDSValue'>"+arr6[d]['TDSAmount']+"</td>";
									if(d == 0)
									{
										table +="<td rowspan='"+arr6.length+"' style='width:200px;text-align: center;text-transform: capitalize;'>"+arr6[d]['Note']+"</td>";
									}
									else
									{
										table += "<td></td>";
									}
									table +="</tr>";
									
									InvoiceTotalAmount +=  parseFloat(arr6[d]['InvoceAmount']);
									TDSTotalAmount += parseFloat(arr6[d]['TDSAmount']);
									
								}
								dues =InvoiceTotalAmount - TDSTotalAmount;
								var amountValue =parseFloat(arr4[0].Amount);
								TotalDues=dues-amountValue ;
								
								table +="<tr><td><br></td></tr>";
								table +="<tr style='background-color: lightgray;'>";
						
								table +="<td style='text-align: center;' align='left' colspan='2'><b>Amount Payable : </b></td>";
								
								table +="<td style='width:100px; font-weight:bold;text-align: center;' >"+InvoiceTotalAmount+"</td>";
								table +="<td style='width:100px; font-weight:bold;text-align: center;'>"+TDSTotalAmount+" <input type='hidden' id='ClearVoucherNo"+iCount+"' name='ClearVoucherNo' value="+ClearVoucherNo+"></td>";
								if(TotalDues > 0)
								{
								table +="<td style='width:100px; color: red; font-weight:bold;text-align: center;'>"+TotalDues +" (Paying less)</td>	";
								}
								else if(TotalDues == 0)
								{
								table +="<td style='width:100px; color: green;font-weight:bold;text-align: center;' >"+TotalDues +" </td>	";	
								}
								else
								{
									table +="<td style='width:100px; color: Blue; font-weight:bold;text-align: center;'>"+TotalDues +" (Paying extra)</td>	";	
								}
								table+="</tr></table>";
								
								document.getElementById('ShowInvoiceData'+ iCount).style.display='block';
								document.getElementById('ShowInvoiceData'+ iCount).value='show';
								document.getElementById('showinvoicetable'+ iCount).style.display='table-row';	
								document.getElementById('RowLabel'+ iCount).style.display='table-row';		
								document.getElementById('ShowMydata'+ iCount).innerHTML=table;
								
							}
							else
							{
								document.getElementById('showinvoicetable'+ iCount).style.display='none';
								document.getElementById('ShowInvoiceData'+ iCount).value='Dontshow';
							//document.getElementById('ShowInvoiceData'+ iCount).style.display='none';	
							}
							
							if(arr4[0].ModeOfPayment != -1)
							{
								document.getElementById('ModeOfPayment' + iCount).value = arr4[0].ModeOfPayment;
								document.getElementById('ModeOfPayment' + iCount).disabled = true;
								document.getElementById('ModeOfPayment' + iCount).style.backgroundColor = 'lightgray';
							}
							if(arr4[0].ExpenseBy != 0)
							{									
								
								//document.getElementById('DE' + iCount).checked=true;
							}
							 
							//document.getElementById('DE' + iCount).disabled=true;
							
							document.getElementById('ChequeDate'+iCount).value=arr4[0].ChequeDate;
							document.getElementById('ChequeDate'+iCount).disabled = true;
							document.getElementById('ChequeDate'+iCount).style.backgroundColor = 'lightgray';
							
							document.getElementById('Amount'+iCount).value=arr4[0].Amount;
							document.getElementById('Amount'+iCount).disabled = true;
							document.getElementById('Amount'+iCount).style.backgroundColor = 'lightgray';
							
							
							
							document.getElementById('ChequeNumber'+iCount).value=arr4[0].ChequeNumber;
							document.getElementById('ChequeNumber'+iCount).disabled = true;
							document.getElementById('ChequeNumber'+iCount).style.backgroundColor = 'lightgray';
							
							document.getElementById('Comment'+iCount).value=arr4[0].Comments;
							document.getElementById('Comment'+iCount).disabled = true;
							document.getElementById('Comment'+iCount).style.backgroundColor = 'lightgray';
														
							document.getElementById('rowid'+iCount).value = arr4[0].id;
							document.getElementById('rowid'+iCount).disabled = true;
							document.getElementById('rowid'+iCount).style.backgroundColor = 'lightgray';
							//alert(arr4[0][14]);
							document.getElementById('PaymentVoucherNo'+iCount).value = arr4[0].PaymentVoucherNo;
							
							document.getElementById('show_in_jvformat'+iCount).value = arr4[0].show_in_jvformat;
							
							document.getElementById('VoucherCounter'+iCount).value = arr4[0].ExternalVoucherNo;
							document.getElementById('OnPageLoadTimeVoucherNumber'+iCount).value = arr4[0].ExternalVoucherNo;
							document.getElementById('VoucherCounter'+iCount).disabled = true;
							document.getElementById('VoucherCounter'+iCount).style.backgroundColor = 'lightgray';
							
							document.getElementById('reconcileStatus'+iCount).value = arr4[0].ReconcileStatus;
							document.getElementById('reconcileStatus'+iCount).disabled = true;
							document.getElementById('reconcileStatus'+iCount).style.backgroundColor = 'lightgray';
													
							document.getElementById('reconcile'+iCount).value = arr4[0].Reconcile;
							document.getElementById('reconcile'+iCount).disabled = true;
							document.getElementById('reconcile'+iCount).style.backgroundColor = 'lightgray';
							
							document.getElementById('return'+iCount).value = arr4[0].Return;
							document.getElementById('return'+iCount).disabled = true;
							document.getElementById('return'+iCount).style.backgroundColor = 'lightgray';
							
							document.getElementById('reconcileDate'+iCount).value = arr4[0].ReconcileDate;
							document.getElementById('reconcileDate'+iCount).disabled = true;
							document.getElementById('reconcileDate'+iCount).style.backgroundColor = 'lightgray';
							
															
							if(arr5.length > 2)
							{
								var iChequeAmount = arr4[0].InvoiceAmount - arr4[0].TDSAmount;														
								var counter = document.getElementById('maxrows').value - 1;	
								var isIDExist = document.getElementById('valRow'+iCount);				
    							if (isIDExist == null){	
									var valRow = "<tr id='valRow"+iCount+"'><td colspan='6'><input type='hidden' id='FinalChqAmount" + iCount+"' name='FinalChqAmount" + iCount+"' value='"+iChequeAmount+"' /></td><td id='ChequeAmount"+iCount+"' style='background-color:#F70D1A;color:#FFF;'></td><td colspan='3'></td><td id='TotalAmount"+iCount+"' style='background-color:#F70D1A;color:#FFF;'></td><td id='TotalTDS"+iCount+"' style='background-color:#F70D1A;color:#FFF;'></td><td></td></tr>";
									$(valRow).insertAfter(document.getElementById('row'+iCount));								
								}								
								for(var c = 2; c < arr5.length; c++)
								{										
								counter = counter + 1;																								
								arr4[0] = arr5[c].split('#');
																
								iChequeAmount = iChequeAmount + (arr4[0].InvoiceAmount - arr4[0].TDSAmount);	
													
								var	table = "<tr id='row"+ counter+"'><td align='center' valign='top' id='Edit"+ counter+"' style='visibility:hidden'></td>";
									table += "<td style='visibility:hidden'><select id='ModeOfPayment" + counter+"' style='width:75px;' name='ModeOfPayment" + counter+"'><option value='0'>CHEQUE</option><option value='1'>ECS</option><option value='2'>OTHER</option></select></td>";
									table += "<td style='visibility:hidden'><input type='text'  id='ChequeNumber" + counter+"' name='ChequeNumber" + counter+"' style='width:70px;' value="+arr4[0].ChequeNumber+" onchange='updateValues("+iCount+");' readonly></td>";	
									table += "<td style='visibility:hidden'><input type='checkbox' style='width:50px;' name='ME"+counter+"' id='ME"+ counter +"' value='"+ counter +"'></td>";
									table +="<td style='visibility:hidden'><input type='text' id='ChequeDate" + counter+"' name='ChequeDate" + counter+"' style='width:70px;background-color:lightgray;' value='"+arr4[0].ChequeDate+"' onchange='updateValues("+iCount+");' disabled></td>";
			
			 						table += "<td><select id='PaidTo" + counter+"' style='width:200px;background-color:lightgray;display:none;' name='PaidTo" + counter+"' onChange='PaidToChanged(this);updateValues("+iCount+");'>"+newArr+" </select></td>";
			
									table += "<td><input type='text' id='Amount" + counter+"' name='Amount" + counter+"' onBlur='extractNumber(this,2,true);ValidateTotal("+iCount+");' onKeyUp='extractNumber(this,2,true);' onKeyPress='return blockNonNumbers(this, event, true, false)' style='width:90px;background-color:lightgray;display:none;' value='"+arr4[0].Amount+"' disabled></td>";
			
									table += "<td><input type='checkbox' style='width:50px;background-color:lightgray;' name='ExpenseTo"+counter+"' id='DE"+ counter +"' value='"+ counter +"' onChange='ValueChanged(this)' disabled></td>";
									
									
									
									
									table += "<td><input type='hidden' id='rowid" + counter+"' name='rowid" + counter+"' value='"+arr4[0].id+"'> </td>";
			
									table += "<td><input type='hidden' id='edit' name='edit' value=''> </td>";
									
									table += "<td><input type='hidden' id='reconcileStatus" + counter+"' name='reconcileStatus" + counter+"' value='"+arr4[0].ReconcileStatus+"'> </td>";
									
									table += "<td><input type='hidden' id='reconcileDate" + counter+"' name='reconcileDate" + counter+"' value='"+arr4[0].ReconcileDate+"'> </td>";
									
									table += "<td><input type='hidden' id='reconcile" + counter+"' name='reconcile" + counter+"' value='"+arr4[0].Reconcile+"'> </td>";
									
									table += "<td><input type='hidden' id='PaymentVoucherNo" + counter+"' name='PaymentVoucherNo" + counter+"' value='"+arr4[0].PaymentVoucherNo+"'> </td>";
									
									table += "<td><input type='hidden' id='return" + counter+"' name='return" + counter+"' value='"+arr4[0].Return+"'> </td>";
									
									table += "<td><input type='hidden' id='ref" + counter+"' name='ref" + counter+"' value='" +iCount+"'> </td>";
									
									table +="</tr>";										
									
																											
									$(table).insertAfter(document.getElementById('row'+iCount));
									document.getElementById('PaidTo' + counter).value = arr4[0].PaidTo;
									
																
								}
																
								document.getElementById('maxrows').value = counter + 1;	
								iCounter = counter + 1;	
								if(document.getElementById('FinalChqAmount'+iCount))
								{
									document.getElementById('FinalChqAmount'+iCount).value = iChequeAmount;
								}
								
																																							
							}
							
							
						}
					}
					else
					{
						
						var iCount = arr3[0];	
						
						document.getElementById('Edit' + iCount).style.visibility = "hidden";							
						
					}					
				}
				
				if(isAnyChequeFill == false)
				{
					document.getElementById("lblEdit").style.visibility = "hidden";		
				}
				
				if(delSetFlag)
				{
					
					getPaymentDetails('delete-' + arr4[0].id);
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

function getPaymentDetails(str)
{  
	var iden	= new Array();
	iden		= str.split("-");

	//$('html, body').animate({ scrollTop: $('#top').offset().top }, 300);

	if(iden[0]=="delete")
	{
		delSetFlag = false;
		var conf = confirm("Are you sure , you want to delete it ?");
		if(conf==1)
		{
			
				document.getElementById("error").innerHTML = "Please Wait...";
			

			remoteCall("ajax/ajaxPaymentDetails.php","form=PaymentDetails&method="+iden[0]+"&PaymentDetailsId="+iden[1],"loadchanges");
			
		}
	}
	else if(iden[0]=="print")
	{
		var pId = iden[1];		
		$.ajax({
			url: 'ajax/ajaxPaymentDetails.php',
        	type: 'POST',
        	data: {"pId": pId, "method":"FetchVoucher"},
        	success: function(data){
            fetchdata = data.split("@@@");
			VoucherData = fetchdata[1].split("#");				
			window.open('print_voucher.php?&vno=' + VoucherData[0] + '&type=' + VoucherData[1]);
			}
		})
	}
	else
	{
		document.getElementById("error").innerHTML = "Please Wait...";
		
		var bankID = document.getElementById('BankID').value;
		remoteCall("ajax/ajaxPaymentDetails.php","form=PaymentDetails&method="+iden[0]+"&PaymentDetailsId="+iden[1]+"&BankId="+bankID,"loadchanges");
	}
}

function loadchanges()
{
	var a		= sResponse.trim();
	var arr1	= new Array();
	var arr2	= new Array();
	arr1		= a.split("@@@");
	arr2		= arr1[1].split("#");


	if(arr1[0] == "edit")
	{
		
	document.getElementById("error").innerHTML = "Please Wait...";

	var maxRowCounter=document.getElementById('maxrows').value;
		
	for(var row=2;row < maxRowCounter;row++)
		{
			document.getElementById('PaidTo' + row).style.display = 'none';
			document.getElementById('Amount' + row).style.display = 'none';
			
			document.getElementById('Comment' + row).style.display = 'none';
			
			document.getElementById('CashRow' + row).style.display = 'none';
			document.getElementById('CashRowLabel' + row).style.display = 'none'; 
		}

		document.getElementById('VoucherDate1').value = arr2[4];
		document.getElementById('Amount1').value=arr2[5];
		document.getElementById('Amount1').focus();
		document.getElementById('PaidTo1').value=arr2[6];
	
		document.getElementById('PayerBank').value=arr2[10];
		document.getElementById('Comment1').value=arr2[12];
		
		document.getElementById('VoucherCounter1').value = arr2[18];
		document.getElementById('OnPageLoadTimeVoucherNumber1').value = arr2[18];
	
		document.getElementById('PaymentVoucherNo1').value=arr2[19];
		if(arr2[20] != null && arr2[20] != "")
		{
			var arr3	= new Array();
			var arr3=JSON.parse(arr2[20]);
			
			var	table='<table width="118%" style="margin-top: -3px;">';
									
			var InvoiceTotalAmount=0;
			var TDSTotalAmount=0;
			var dues=0;
			var TotalDues=0;
			var ClearVoucherNo=0;
			
			table+="<tr style='background-color: #d9edf7; height:22px;line-height: 20px;' align='center'>"
			table +="<th style='width:15%;text-align: center;'>Invoice date</th>";
			table +="<th style='width:35%;text-align: center;'>Expense To</th>";
			table +="<th style='width:15%;text-align: center;'>Invoice Amount</th>";
			table +="<th style='width:15%;text-align: center;'>TDS Amount</th>";
			table +="<th style='width:30%;text-align: center;'>Comments</th>";
			table+="</tr>"
			for(var d=0;d < arr3.length;d++)
			{   
				ClearVoucherNo=arr3[d]['ExpenseDetails']['InvoiceClearedVoucherNo'];
				
				table+="<tr>"
				table +="<td style='width:100px;text-align: center;'><b>"+arr3[d]['ExpenseDetails']['date']+"</b></td>";
				table +="<td style='width:200px ;text-align: center;'><b>"+arr3[d]['ExpenseDetails']['ledger_name']+"</b></td>";
				table +="<td style='width:100px ;font-weight: bold;text-align: center;' id='InvoiceValue'>"+arr3[d]['InvoceAmount']+"</td>";
				table +="<td style='width:100px;font-weight: bold;text-align: center;'  id='TDSValue'>"+arr3[d]['TDSAmount']+"</td>";
				if(d == 0)
				{
					table +="<td rowspan='"+arr3.length+"' style='width:200px;text-align: center;text-transform: capitalize;'>"+arr3[d]['Note']+"</td>";
				}
				else
				{
					table += "<td></td>";
				}
				table +="</tr>";
										
				InvoiceTotalAmount +=  parseFloat(arr3[d]['InvoceAmount']);
				TDSTotalAmount += parseFloat(arr3[d]['TDSAmount']);
			}
			dues =InvoiceTotalAmount - TDSTotalAmount;
			var amountValue =parseFloat(arr2[5]);
			TotalDues=dues-amountValue ;
			table +="<tr><td><br></td></tr>";
			table +="<tr style='background-color: lightgray;'>";
			table +="<td style='text-align: center;' align='left' colspan='2'><b>Amount Payable : </b></td>";
			table +="<td style='width:100px; font-weight:bold;text-align: center;' >"+InvoiceTotalAmount+"</td>";
			table +="<td style='width:100px; font-weight:bold;text-align: center;'>"+TDSTotalAmount+" <input type='hidden' id='ClearVoucherNo"+1+"' name='ClearVoucherNo' value="+ClearVoucherNo+"></td>";
			
			if(TotalDues > 0)
			{
				table +="<td style='width:100px; color: red; font-weight:bold;text-align: center;'>"+TotalDues +" (Paying less)</td>	";
			}
			else if(TotalDues == 0)
			{
				table +="<td style='width:100px; color: green;font-weight:bold;text-align: center;' >"+TotalDues +" </td>	";	
			}
			else
			{
				table +="<td style='width:100px; color: Blue; font-weight:bold;text-align: center;'>"+TotalDues +" (Paying extra)</td>	";	
			}
			table+="</tr></table>";
			document.getElementById('ShowInvoiceData'+ 1).style.display='block';
			document.getElementById('ShowInvoiceData'+ 1).value='show';
			document.getElementById('showinvoicetable'+ 1).style.display='table-row';	
			//document.getElementById('CashRowLabel'+ row).style.display='table-row';		
			document.getElementById('ShowMydata'+ 1).innerHTML=table;
		}
		else
		{
			//document.getElementById('showinvoicetable'+ iCount).style.display='none';
			//document.getElementById('ShowInvoiceData'+ iCount).value='Dontshow';
		}
		
		
		if(ClearVoucherNo != null)
		{
			document.getElementById('Submit').innerHTML="Update";
			document.getElementById('Submit').onclick=function(){ValueChanged(true,1)};
			
			//document.getElementById('Submit').disable="true";
			return;
		}
		else
		{
		document.getElementById('Submit').innerHTML="Update";
		document.getElementById('Submit').onclick=function(){ UpdateCashPaymentDetails(arr2[0],arr2[4],minGlobalCurrentYearStartDate , maxGlobalCurrentYearEndDate)};
		document.getElementById('SubmitUp').innerHTML="Update";
		document.getElementById('SubmitUp').onclick=function(){ UpdateCashPaymentDetails(arr2[0],arr2[4],minGlobalCurrentYearStartDate , maxGlobalCurrentYearEndDate)};
		}
		if(delSetFlag)
		{
			getPaymentDetails('delete-' + arr2[0]);
		}
	}
	else if(arr1[0] == "delete")
	{
		
			document.getElementById("error").innerHTML = "Please Wait...";
		
		var BankID=document.getElementById('BankID').value;
		var LeafID=document.getElementById('LeafID').value;
		var CustomLeaf=document.getElementById('CustomLeaf').value;

		var out = document.getElementById('edit').value;
					
		if(out != "")
		{
			localStorage.setItem('refreshPage', "1");
			window.close();				
		}
		else
		{
			window.location.href ="PaymentDetails.php?bankid=" + BankID + "&LeafID=" + LeafID + "&CustomLeaf=" + CustomLeaf;	
		}
		
	}
	document.getElementById("error").innerHTML = "";
}


function UpdateCashPaymentDetails(CashDetailsId,VoucherDate,minGlobalCurrentYearStartDate , maxGlobalCurrentYearEndDate)
{
	
	document.getElementById('Submit').disabled = true;
	document.getElementById('SubmitUp').disabled = true;
	
	var VoucherDate = document.getElementById('VoucherDate1').value;
 	var BankID = document.getElementById('BankID').value;
	var PayerBank = document.getElementById('PayerBank').value;
	var LeafID = document.getElementById('LeafID').value;
	var VoucherNumber = document.getElementById('VoucherCounter1').value;
	var SystemDefineVNo= document.getElementById('Current_Counter1').value;
	var arRowsToSubmit = [];
	var iSubmittedData = 0;
	var PaidTo = document.getElementById('PaidTo1').value;
	var iChequeNumber = '-1';
	var ChequeDate = -1;
	var iAmount = document.getElementById('Amount1').value;
	var ExpenseBy = 0;
	var chkState = false;
	var VoucherDateFlag = "";
	var InvoiceDate2 = "";
	var iTDSAmount = 0;
	var Comment = document.getElementById('Comment1').value;
	var OnPageLoadTimeVoucherNumber = document.getElementById('OnPageLoadTimeVoucherNumber1').value;
	var iCheckState = 0;
	var IsCallUpdtCnt = 1;
	var mode = 3;
	
	if(chkState)
	{
		iCheckState = 1;
	}
	
	if(LeafID == -1)
	{
		if(jsdateValidator('VoucherDate1', VoucherDate , minGlobalCurrentYearStartDate , maxGlobalCurrentYearEndDate) == true)
		{
			VoucherDateFlag = true;	
		}
		else
		{
			VoucherDateFlag = false;		
		}
		
	}
	
	var isDuplicateCounter =  IsCounterDuplicate(VoucherNumber,mode,OnPageLoadTimeVoucherNumber);
	
	if(isDuplicateCounter == false)
	{
		document.getElementById('Submit').disabled = false;
		document.getElementById('SubmitUp').disabled = false;
		return false;
	}
	
	
	if(VoucherNumber != SystemDefineVNo)
	{
		IsCallUpdtCnt = UserResponseOnExCount(VoucherNumber,SystemDefineVNo);
	}
	
	if(PaidTo == 0 || PaidTo == -1 || iChequeNumber == "" || ChequeDate == "" ||  iAmount == "" || (iCheckState == 1 && ExpenseBy == 0) || (VoucherNumber == "" || VoucherNumber == 0)|| (iCheckState == 1 && InvoiceDate2 == "") || VoucherDateFlag == false)
	{
		
		document.getElementById('Submit').disabled = false;
		document.getElementById('SubmitUp').disabled = false;
		
		var EmptyField = "";
		if(iCheckState == 1 && ExpenseBy == 0)
		{
			if(EmptyField.length == 0)
			{
				EmptyField = "ExpenseTo";
			}
			else
			{
				EmptyField += ", ExpenseTo";
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
		
		if(iCheckState == 1 && InvoiceDate2 == "")
		{
			if(EmptyField.length == 0)
			{
				EmptyField = "InvoiceDate";
			}
			else
			{
				EmptyField += ", InvoiceDate";
			}
			
		}
		if(LeafID == -1 && VoucherDateFlag == false)
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
		
		if(PaidTo == 0 ||  PaidTo == -1)
		{
			if(EmptyField.length == 0)
			{
				EmptyField = "PaidTo";
			}
			else
			{
				EmptyField += ",PaidTo";
			}
			
		}
		
		if(VoucherNumber == "" || VoucherNumber == 0)
		{
			if(VoucherCounter == "")
			{
				EmptyField = "Voucher Number";
			}
		}
		document.getElementById('label1').innerHTML = "Please enter value for " + EmptyField +".";
		document.getElementById('label1').style.color = '#FF0000';
		
		setTimeout('timeout(label1' +')', 5000);
	}
	else
	{		
		var obj = "";
		if(LeafID == -1)
		{
		obj = {"PaidTo" : PaidTo, "ChequeNumber" : iChequeNumber, "VoucherNumber":VoucherNumber,"OnPageLoadTimeVoucherNumber":OnPageLoadTimeVoucherNumber,"IsCallUpdtCnt":IsCallUpdtCnt, "ChequeDate" : "0000-00-00", "Amount" : iAmount, "PayerBank" : PayerBank, "Comments" : Comment, "VoucherDate" : VoucherDate,"ExpenseBy" : ExpenseBy,"DoubleEntry" : iCheckState,
		"LeafID" : LeafID,"InvoiceDate" :InvoiceDate2,"TDSAmount" :iTDSAmount};
		}
		arRowsToSubmit.push(obj);
		if(LeafID == -1)
		{
			document.getElementById('PaidTo1').value ='';
			document.getElementById('Amount1').value = "";
			document.getElementById('Comment1').value = "";
			
		}
			
	var objData = {'data' : JSON.stringify(arRowsToSubmit), "method" : 'UpdateCashPaymentDetails',"CashDetailsId" : CashDetailsId}; 
	showLoader();	
	$.ajax({
			url : "ajax/ajaxPaymentDetails.php",
			type : "POST",
			data: objData ,
			success : function(data)
			{	
				alert('Data Submitted Successfully');
				if(LeafID == -1)
				{
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


function dbUpdated()
{
	var sResponse = getResponse(RESPONSETYPE_STRING, true);
	
}
function SubmitChequeDetails(minGlobalCurrentYearStartDate , maxGlobalCurrentYearEndDate)
{	
	document.getElementById('Submit').disabled = true;
	document.getElementById('SubmitUp').disabled = true;
	
	var VoucherDate; 
	var iRowCount = document.getElementById('maxrows').value;	
	var PayerBank = document.getElementById('PayerBank').value;
	var LeafID = document.getElementById('LeafID').value;
	var ExVoucherType = document.getElementById('VoucherType').value;
	var IsCallUpdtCnt = 1;
	
	var ExpenseBy = 0;
	var InvoiceDate2;
	var iTDSAmount;
	var arRowsToSubmit = [];
	var iSubmittedData = 0; 
	var DateFlag = true;
	var VoucherDateFlag = true;
	var IsInvoiceDateValid = true;
	var isReadyToSubmit = false;
	var iInvoiceAmount;
	var arChequeNumber =  [];
	var NumberofEntry = 0;
	var mode = 1;
	for(var iRows = 1 ; iRows < iRowCount; iRows++)
	{
		var VoucherNumber;
		var str = 'PaidTo'+ iRows;	
		var iSocietyID = document.getElementById('PaidTo'+ iRows).value;
		if(iSocietyID != 0 && LeafID != "")
		{
			if(LeafID == -1)
			{
				VoucherDate = document.getElementById('VoucherDate'+ iRows).value;
			}
			
			VoucherNumber = document.getElementById('VoucherCounter'+iRows).value;
			
			systemDefineVNo = document.getElementById('Current_Counter'+iRows).value;
			
			
			var PaidTo = document.getElementById('PaidTo'+ iRows).value;
			var LeafID = document.getElementById('LeafID').value;
			var rowid = document.getElementById('rowid'+ iRows).value;
			
			
			var iChequeNumber = 0;
			if(LeafID != -1)
			{
				iChequeNumber = document.getElementById('ChequeNumber'+ iRows).value.replace(/\s/g,'');
			}
			else
			{
				iChequeNumber = '-1';
			}
			var chequeID = 0;//Cheque Number And Mode of payment combination
			if(LeafID != -1)
			{
				chequeID = document.getElementById('ModeOfPayment'+ iRows).value + ":" +iChequeNumber;
				VoucherNumber = 0;
			}
			
			if(VoucherNumber != 0)
			{
				//In CounterDuplicate 1 is add mode
				var Isduplicate = IsCounterDuplicate(VoucherNumber,mode,0);
				if(Isduplicate == false)
				{
					document.getElementById('Submit').disabled = false;
					document.getElementById('SubmitUp').disabled = false;
					return false;		
				}
				
				if(VoucherNumber != systemDefineVNo)
				{
					IsCallUpdtCnt = UserResponseOnExCount(VoucherNumber,systemDefineVNo);
				}
				
			}

			if(arChequeNumber.length == 0)
			{
				
				arChequeNumber.push(chequeID);	
			}
			else
			{
				var iChqIndex = FindValueInArray(arChequeNumber, chequeID);
				if(iChqIndex != true)
				{
					arChequeNumber.push(chequeID);
				}
				else
				{
					var bMultEntryChecked = false;
					if(LeafID != -1)
					{
						var ireferenceRow = document.getElementById('ref'+ iRows).value;
						//bMultEntryChecked = document.getElementById('ME'+ ireferenceRow).checked;		
					}
					if(bMultEntryChecked == false)
					{
						if(rowid > 0)
						{
							continue;	
						}
						if(LeafID != -1)
						{
							if(document.getElementById('ModeOfPayment'+ iRows).value == 0)
							{
								document.getElementById('label'+ iRows).innerHTML = "Cheque Number " + iChequeNumber +" should not repeat for this slip.";
							}
							else if(document.getElementById('ModeOfPayment'+ iRows).value == 1)
							{
								document.getElementById('label'+ iRows).innerHTML = "Transaction Number " + iChequeNumber +" should not repeat for this slip.";
							}
							else if(document.getElementById('ModeOfPayment'+ iRows).value == 2)
							{
								//Do nothing . Allow any number of cheque number for Other payment mode
							}
							else
							{
								document.getElementById('label'+ iRows).innerHTML = "Cheque Number " + iChequeNumber +" should not repeat for this slip.";
							}
							if(document.getElementById('ModeOfPayment'+ iRows).value != 2)
							{
								document.getElementById('label'+ iRows).style.color = '#FF0000';	
								setTimeout('timeout(label'+ iRows +')', 15000);
								document.getElementById('Submit').disabled = false;
								document.getElementById('SubmitUp').disabled = false;
								return;
							}
						}
						
					}
				}
			}
			if(rowid > 0)
			{
				//rows submitted already wont be considerred to submit again. 
				continue;	
			}
			var ChequeDate = 0;
			if(LeafID != -1)
			{
				ChequeDate = document.getElementById('ChequeDate'+ iRows).value;
			}
			else
			{
				ChequeDate = -1;
			}
			var iAmount = document.getElementById('Amount'+ iRows).value;
			var chkState = false;
			var ref = 0;
			var isMEChecked = false;
			var iModeOfPayment = 0;
			var iCheckState = 0;
			if(chkState)
			{
				iCheckState = 1;
			}
			else
			{
				iTDSAmount=0;
				ExpenseBy =0;
				InvoiceDate2="00-00-0000";
				iInvoiceAmount=0;
			}
		
			
			if(LeafID != -1 && ChequeDate != "" &&  PaidTo > 0)
			{
				if(jsdateValidator('ChequeDate'+ iRows , ChequeDate , minGlobalCurrentYearStartDate , maxGlobalCurrentYearEndDate) == true)
				{
					//return true;
					DateFlag = true;	
				}
				else
				{
					
					DateFlag = false;		
				}
				//alert("jsdateval 1");
			}
			
			if(iCheckState == 1)
			{
				
				if(jsdateValidator('InvoiceDate'+ iRows , InvoiceDate2 , minGlobalCurrentYearStartDate , maxGlobalCurrentYearEndDate) == true)
				{
					IsInvoiceDateValid = true;			
				}
				else
				{
					IsInvoiceDateValid = false;
				}
				//alert("jsdateval 2");
			}
			
			if(LeafID == -1)
			{
				if(jsdateValidator('VoucherDate'+ iRows, VoucherDate , minGlobalCurrentYearStartDate , maxGlobalCurrentYearEndDate) == true)
				{
					//return true;
					VoucherDateFlag = true;	
				}
				else
				{
					VoucherDateFlag = false;		
				}
				//alert("jsdateval 3");
			}
			
			var isInvoiceAmountValid = true;
			if(iCheckState == 1 && isMEChecked == false && iInvoiceAmount != (parseInt(iAmount) + parseInt(iTDSAmount = iTDSAmount ? iTDSAmount : 0)))
			{
				isInvoiceAmountValid = false;
			}
			
			var iChequeAmount = 0;
			if(document.getElementById('FinalChqAmount'+iRows))
			{
				iChequeAmount = document.getElementById('FinalChqAmount'+iRows).value;
			}								
			//alert("trace");
			if(PaidTo == 0 || PaidTo == -1 || iChequeNumber == "" || ChequeDate == "" ||  iAmount == "" || (iCheckState == 1 && (ExpenseBy == 0 || ExpenseBy == -1)) ||  (iCheckState == 1 && InvoiceDate2 == "") || IsInvoiceDateValid == false || DateFlag == false || (LeafID == -1 && VoucherDateFlag == false) || (iChequeAmount > 0 && iChequeAmount != iAmount) || (iCheckState == 1 && iInvoiceAmount == "") || (isMEChecked == true && iCheckState != 1) || isInvoiceAmountValid == false)
			{
																			
				document.getElementById('Submit').disabled = false;
				document.getElementById('SubmitUp').disabled = false;
				
				if(iChequeAmount > 0 && iChequeAmount != iAmount)
				{					
					document.getElementById('label'+ iRows).innerHTML = "Cheque amounts does not match.";
					document.getElementById('label'+ iRows).style.color = '#FF0000';
					setTimeout('timeout(label'+ iRows +')', 5000);
					return;
				}
				
				var EmptyField = "";
				if(iCheckState == 1 && ExpenseBy == 0)
				{
					if(EmptyField.length == 0)
					{
						EmptyField = "ExpenseTo";
					}
					else
					{
						EmptyField += ", ExpenseTo";
					}
				}
				
				//alert("test4");
				if(( iChequeNumber.length == 0 && LeafID != -1 ) ||  ( iChequeNumber.length == 0 && LeafID != -1 && document.getElementById('ModeOfPayment'+ iRows).value != 2 ))
				{
					if(EmptyField.length == 0)
					{
						EmptyField = "ChequeNumber";
					}
					else
					{
						
						EmptyField += ", ChequeNumber";
					}
				}
				
				if(( ChequeDate.length == 0 && LeafID != -1 ) || ( DateFlag == false && LeafID != -1 ) )
				{
					if(EmptyField.length == 0)
					{
						EmptyField = "ChequeDate";
					}
					else
					{
						
						EmptyField += ", ChequeDate";
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
				
				if(( iCheckState == 1 && InvoiceDate2 == "" ) || ( IsInvoiceDateValid == false && LeafID != -1 ) )
				{
					if(EmptyField.length == 0)
					{
						EmptyField = "InvoiceDate";
					}
					else
					{
						EmptyField += ", InvoiceDate";
					}
					
				}
				
				if(LeafID == -1 && VoucherDateFlag == false)
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
				
				if(PaidTo == 0 ||  PaidTo == -1)
				{
					if(EmptyField.length == 0)
					{
						EmptyField = "PaidTo";
					}
					else
					{
						EmptyField += ",PaidTo";
					}
					
				}
				
				if(iCheckState == 1 && iInvoiceAmount == "")
				{
					if(EmptyField.length == 0)
					{
						EmptyField = "Invoice amount";
					}
					else
					{
						EmptyField += ",Invoice amount";
					}
				}
				
				if(EmptyField.length != 0)
				{		
					document.getElementById('label'+ iRows).innerHTML = "Please enter valid value for " + EmptyField +".";
					document.getElementById('label'+ iRows).style.color = '#FF0000';
					setTimeout('timeout(label'+ iRows +')', 5000);
					document.getElementById('Submit').disabled = false;
					document.getElementById('SubmitUp').disabled = false;
					return;	
				}
				
				if(isMEChecked == true && iCheckState != 1)
				{
					document.getElementById('label'+ iRows).innerHTML = "Multiple entry must be double entry";
					document.getElementById('label'+ iRows).style.color = '#FF0000';
					setTimeout('timeout(label'+ iRows +')', 5000);
					document.getElementById('Submit').disabled = false;
					document.getElementById('SubmitUp').disabled = false;
					return;	
				}
				
				if(isInvoiceAmountValid == false)
				{
					document.getElementById('label'+ iRows).innerHTML = "Invalid invoice amount";
					document.getElementById('label'+ iRows).style.color = '#FF0000';
					setTimeout('timeout(label'+ iRows +')', 5000);
					document.getElementById('Submit').disabled = false;
					document.getElementById('SubmitUp').disabled = false;
					return;
				}
								
				if(document.getElementById('ModeOfPayment'+ iRows).value != 2)
				{												
					continue;
				}
				else if(document.getElementById('ModeOfPayment'+ iRows).value == 2 && ( ChequeDate == "" ||  iAmount == "" || (iCheckState == 1 && ExpenseBy == 0) ||  (iCheckState == 1 && InvoiceDate2 == "")))
				{					
					continue;
				}
				
			}
			else
			{
				//alert("validation missed");
			}
			var Comment ='';
			var Comment = document.getElementById('Comment'+ iRows).value;
			if(LeafID != -1)
			{
			//var isMultipleEntry = document.getElementById('ME'+ iRows).checked;
			var isMultipleEntry=false;
			}
			var obj = "";
			if(LeafID != -1) //other than cash entry
			{
				iModeOfPayment = document.getElementById('ModeOfPayment'+ iRows).value;
		
				if(customleaf != -1)
				{
					VoucherNumber = parseInt(document.getElementById('Current_Counter'+iRows).value);	
					VoucherNumber += NumberofEntry;
					NumberofEntry++;
					
				}
				else
				{
					VoucherNumber = document.getElementById('VoucherCounter'+iRows).value;
				}
				
				
											
				obj = {"SocietyID" : iSocietyID, "PaidTo" : PaidTo, "ChequeNumber" : iChequeNumber, "ChequeDate" : ChequeDate, "Amount" : iAmount, "PayerBank" : PayerBank, "Comments" : Comment, "VoucherDate" : ChequeDate, "VoucherNumber":VoucherNumber,"systemDefineVNo":systemDefineVNo,"IsCallUpdtCnt":IsCallUpdtCnt,"ExpenseBy" : ExpenseBy,"DoubleEntry" : iCheckState, "update" : 'update',
			"LeafID" : LeafID,"InvoiceDate" :InvoiceDate2,"TDSAmount" :iTDSAmount,"ModeOfPayment" : iModeOfPayment, "MultipleEntry":isMultipleEntry, "Ref":ref, "InvoiceAmount":iInvoiceAmount};
				//alert(JSON.stringify(obj));
			}
			else
			{
				//cash entry				
				obj = {'Row' : iRows,"SocietyID" : iSocietyID, "PaidTo" : PaidTo, "ChequeNumber" : iChequeNumber, "ChequeDate" : "0000-00-00", "Amount" : iAmount, "PayerBank" : PayerBank, "Comments" : Comment, "VoucherDate" : VoucherDate, "VoucherNumber": VoucherNumber,"systemDefineVNo":systemDefineVNo,"IsCallUpdtCnt":IsCallUpdtCnt, "ExpenseBy" : ExpenseBy,"DoubleEntry" : iCheckState,
			"LeafID" : LeafID,"InvoiceDate" :InvoiceDate2,"TDSAmount" :iTDSAmount,"ModeOfPayment" : "-1"};
			}
			arRowsToSubmit.push(obj);
			iSubmittedData++;
	
		}
		//alert(iRows);
		
	}
	if(iSubmittedData == 0)
	{
		document.getElementById('Submit').disabled = false;
		document.getElementById('SubmitUp').disabled = false;
		return;
	}  
	isReadyToSubmit = true;
	//alert("ready to submit data");
		
	if(isReadyToSubmit)
	{
		var objData = {'data' : JSON.stringify(arRowsToSubmit), "update" : 'update'}; 
		showLoader();
		$.ajax({
				url : "ajax/ajaxPaymentDetails.php",
				type : "POST",
				data: objData ,
				success : function(data)
				{	
					//alert(data);
					if(data.trim() == "@@@")
					{
						
						alert('Data Submitted Successfully');
						for(var iRows = 1 ; iRows < iRowCount; iRows++)
						{
							
							if(LeafID == -1)
							{
								document.getElementById('PaidTo'+ iRows).value ='';
							}
							else
							{
								document.getElementById('PaidTo'+ iRows).disabled = true;
								document.getElementById('PaidTo'+ iRows).style.backgroundColor = 'lightgray';
							}
							//alert(iPaidBy);
							if(LeafID == -1)
							{
								//document.getElementById('ChequeNumber'+ iRows).value = "";
							}
							else
							{				
								document.getElementById('ChequeNumber'+ iRows).disabled = true;
								document.getElementById('ChequeNumber'+ iRows).style.backgroundColor = 'lightgray';
							}
							
							if(LeafID == -1)
							{
								//document.getElementById('ChequeDate'+ iRows).value = "";
							}
							else
							{
								document.getElementById('ChequeDate'+ iRows).disabled = true;
								document.getElementById('ChequeDate'+ iRows).style.backgroundColor = 'lightgray';
							}
							//alert(ChequeDate);
							
							if(LeafID == -1)
							{
								//document.getElementById('ChequeDate'+ iRows).value = "";
							}
							else
							{
								//document.getElementById('InvoiceDate'+ iRows).disabled = true;
								//document.getElementById('InvoiceDate'+ iRows).style.backgroundColor = 'lightgray';
							}
							
							if(LeafID == -1)
							{
								document.getElementById('Amount'+ iRows).value = "";
							}
							else
							{
								//document.getElementById('Amount'+ iRows).disabled = true;
								document.getElementById('Amount'+ iRows).style.backgroundColor = 'lightgray';
							}
							
							if(LeafID == -1)
							{
								//document.getElementById('TDSAmount'+ iRows).value = "";
							}
							else
							{
								//document.getElementById('TDSAmount'+ iRows).disabled = true;
								//document.getElementById('TDSAmount'+ iRows).style.backgroundColor = 'lightgray';
							}
							
							if(LeafID == -1)
							{
								//document.getElementById('Comment'+ iRows).value = "";
							}
							else
							{
								//document.getElementById('Comment'+ iRows).disabled = true;
								//document.getElementById('Comment'+ iRows).style.backgroundColor = 'lightgray';
							}
							if(LeafID == -1)
							{
								/*if(!document.getElementById('ExpenseTo'+ iRows).disabled)
								{
									document.getElementById('ExpenseTo'+ iRows).value = '';
								}*/
							}
							else
							{
								//document.getElementById('ExpenseTo'+ iRows).disabled = true;
								//document.getElementById('ExpenseTo'+ iRows).style.backgroundColor = 'lightgray';
								document.getElementById('DE' + iRows).disabled=true;
							}	
						}
						if(LeafID == -1 && iSubmittedData > 0)
						{
							location.reload(true);
						}
						location.reload(true);
						window.location.reload(true);
						
					}
					else
					{
						
						var strError = data.replace("@@@","");
						strError  = strError.trim();
						var re = /\s*,\s*/;
						var arErrors = [];
						var arUniqueErrors = [];
						arErrors = strError.split(re);
						console.log(arErrors);
						$.each(arErrors, function(i, el){
							if($.inArray(el, arUniqueErrors) == -1) arUniqueErrors.push(el);
						});
						//*** Error *** 
						alert("*** Error *** : \n" + arUniqueErrors.join("\n"));
						//alert(arUniqueErrors.length);
						document.getElementById('Submit').disabled = false;
						document.getElementById('SubmitUp').disabled = false;
						return;
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
			});//remoteCallNew(sURL, objData, 'dbUpdated');	
	
	}
	
}

function EditChequeDetails(RowCounter,minGlobalCurrentYearStartDate , maxGlobalCurrentYearEndDate)
{	
	var mainRow = RowCounter;		
	counterArray = [];
	counterArray.push(RowCounter);	
	createCouterArray(RowCounter);		
	document.getElementById('Submit').disabled = true;
	document.getElementById('SubmitUp').disabled = true;	
	document.getElementById('Submit' + RowCounter).disabled = true;	
	 	
	var arRowsToSubmit = [];				
	for(var c = 0; c < counterArray.length; c++)
	{
		RowCounter = counterArray[c];
		var DateFlag = "";
		var IsCallUpdtCnt = 1;
		var IsInvoiceDateValid = true;
		if(LeafID == -1)
		{
			var VoucherDate = document.getElementById('VoucherDate').value;
		}
		var PayerBank = document.getElementById('PayerBank').value;
		var LeafID = document.getElementById('LeafID').value;		
		var CustomLeaf=document.getElementById('CustomLeaf').value;
		var iModeOfPayment = 0;
		
		var iChequeNumber = 0;
		if(LeafID != -1)
		{
			iChequeNumber = document.getElementById('ChequeNumber'+ RowCounter).value.replace(/\s/g,'');
			iModeOfPayment = document.getElementById('ModeOfPayment'+ RowCounter).value;
		}
		else
		{
			iChequeNumber = '-1';
		}
		//alert(iChequeNumber);
		var ChequeDate = 0;
		var isMultipleEntry = 0;
		var ref = 0;
		var isMEChecked = 0;
		if(LeafID != -1)
		{
			ChequeDate = document.getElementById('ChequeDate'+ RowCounter).value;	
			//isMultipleEntry = document.getElementById('ME'+ RowCounter).checked;	
			isMultipleEntry =false;
			ref = document.getElementById('ref'+ RowCounter).value;	
			//isMEChecked = document.getElementById('ME'+ ref).checked;
			isMEChecked =false;
		}
		else
		{
			ChequeDate = -1;
		}		
		var chkState = document.getElementById('DE' + RowCounter).checked;
		var PaidTo = document.getElementById('PaidTo'+ RowCounter).value;
		var iAmount = document.getElementById('Amount'+ RowCounter).value;
		var VoucherNumber = document.getElementById('VoucherCounter'+ RowCounter).value;
		var CurrentExCounter = document.getElementById('Current_Counter'+ RowCounter).value;
		var Comment = document.getElementById('Comment'+ RowCounter).value;
		//var Comment ='';
		var rowid = document.getElementById('rowid'+ RowCounter).value;
		var reconcileDate = document.getElementById('reconcileDate'+ RowCounter).value;
		var reconcileStatus = document.getElementById('reconcileStatus'+ RowCounter).value;
		var reconcile = document.getElementById('reconcile'+ RowCounter).value;
		var returnFlag = document.getElementById('return'+ RowCounter).value;
		var OnPageLoadTimeVoucherNumber = document.getElementById('OnPageLoadTimeVoucherNumber'+ RowCounter).value;
		var mode = 3;
		var iChequeAmount;			
		
		
		var Isduplicate = IsCounterDuplicate(VoucherNumber,mode,OnPageLoadTimeVoucherNumber);
	
		if(Isduplicate == false)
		{
			document.getElementById('Submit').disabled = false;
			document.getElementById('SubmitUp').disabled = false;	
			document.getElementById('Submit' + RowCounter).disabled = false;
			return false;		
		}
		
		if(VoucherNumber != CurrentExCounter)
		{
			
			IsCallUpdtCnt = UserResponseOnExCount(VoucherNumber,CurrentExCounter);
		}
		
		if(document.getElementById('FinalChqAmount'+mainRow))
		{
			iChequeAmount = document.getElementById('FinalChqAmount'+mainRow).value;	
			iChequeAmount = parseFloat(iChequeAmount);					
			if((iChequeAmount > 0 || document.getElementById('Amount'+ mainRow).value > 0) && (iChequeAmount != document.getElementById('Amount'+ mainRow).value))
			{				
				document.getElementById('label'+ mainRow).innerHTML = "Cheque amount does not match";
				document.getElementById('label'+ mainRow).style.color = '#FF0000';
				setTimeout('timeout(label'+ mainRow +')', 5000);
				document.getElementById('Submit' + mainRow).disabled = false;
				return;
			}
		}
		
		var iCheckState = 0;
		if(chkState)
		{
			iCheckState = 1;
		}
		else
		{
			ExpenseBy = 0;
			iTDSAmount = 0;
			InvoiceDate2="0000-00-00";
			iInvoiceAmount = 0;
		}
		
		if(LeafID != -1)
		{
			if(jsdateValidator('ChequeDate'+ RowCounter , ChequeDate , minGlobalCurrentYearStartDate , maxGlobalCurrentYearEndDate) == true)
			{
				//return true;
				DateFlag = true;	
			}
			else
			{
				DateFlag = false;		
			}
			
		}
		
		
		if(iCheckState == 1 && InvoiceDate2.length > 0)
		{
			
			if(jsdateValidator('InvoiceDate'+ RowCounter , InvoiceDate2 , minGlobalCurrentYearStartDate , maxGlobalCurrentYearEndDate) == true)
			{
				IsInvoiceDateValid = true;			
			}
			else
			{
				IsInvoiceDateValid = false;
			}
		}
		
		var isInvoiceAmountValid = true;
		if(iCheckState == 1 && isMEChecked == false && iInvoiceAmount != (parseInt(iAmount) + parseInt(iTDSAmount = iTDSAmount ? iTDSAmount : 0)))
		{
			isInvoiceAmountValid = false;
		}
				
	if(PaidTo == 0 || PaidTo == -1 || (iModeOfPayment != 2 && iChequeNumber == "") || ChequeDate == "" ||  iAmount == "" || (iCheckState == 1 && (ExpenseBy == 0 || ExpenseBy == -1)) ||  (iCheckState == 1 && InvoiceDate2 == "") || IsInvoiceDateValid == false || DateFlag == false || (iCheckState == 1 && iInvoiceAmount == "") || (isMEChecked == true && iCheckState != 1) || isInvoiceAmountValid == false)
	{
		
		document.getElementById('Submit').disabled = false;
		document.getElementById('SubmitUp').disabled = false;
		document.getElementById('Submit' + mainRow).disabled = false;
		
		var EmptyField = "";
		if(iCheckState == 1 && ExpenseBy == 0)
		{
			if(EmptyField.length == 0)
			{
				EmptyField = "ExpenseTo";
			}
			else
			{
				EmptyField += ", ExpenseTo";
			}
		}//alert(ChequeDate.length);
		
		if(PaidTo == 0 || PaidTo == -1)
		{
			if(EmptyField.length == 0)
			{
				EmptyField = "PaidTo";
			}
			else
			{
				EmptyField += ", PaidTo";
			}
		}
		
		if(iChequeNumber == "")
		{
			if(EmptyField.length == 0)
			{
				EmptyField = "ChequeNumber";
			}
			else
			{
				EmptyField += ", ChequeNumber";
			}
		}
		
		
		if(ChequeDate == ""  || DateFlag == false)
		{
			if(EmptyField.length == 0)
			{
				EmptyField = "ChequeDate";
			}
			else
			{
				EmptyField += ", ChequeDate";
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
		
		if((iCheckState == 1 && InvoiceDate2 == "") || IsInvoiceDateValid == false)
		{
			if(EmptyField.length == 0)
			{
				EmptyField = "InvoiceDate";
			}
			else
			{
				EmptyField += ", InvoiceDate";
			}
			
		}
		
		if(iCheckState == 1 && iInvoiceAmount == "")
		{
			if(EmptyField.length == 0)
			{
				EmptyField = "Invoice amount";
			}
			else
			{
				EmptyField += ",Invoice amount";
			}
		}
		
		document.getElementById('label'+ mainRow).innerHTML = "Please enter valid value for " + EmptyField +".";
		if(isMEChecked == true && iCheckState != 1)
		{
			document.getElementById('label'+ mainRow).innerHTML = "Multiple entry must be double entry";
		}
		if(isInvoiceAmountValid == false)
		{
			document.getElementById('label'+ mainRow).innerHTML = "Invalid invoice amount.";
		}
		document.getElementById('label'+ mainRow).style.color = '#FF0000';
		//var strLabel = 'label'+ iRows;
		setTimeout('timeout(label'+ mainRow +')', 5000);
		return;
	}
	else
	{	
		if(iCheckState != 1)
		{
			InvoiceDate2 = "";
		}
	var obj = ""; 	 
			obj = {"PaidTo" : PaidTo, "ChequeNumber" : iChequeNumber, "ChequeDate" : ChequeDate, "Amount" : iAmount, "PayerBank" : PayerBank, "Comments" : Comment, "VoucherDate" : ChequeDate, "VoucherNumber":VoucherNumber, "OnPageLoadTimeVoucherNumber":OnPageLoadTimeVoucherNumber,"IsCallUpdtCnt":IsCallUpdtCnt, "ExpenseBy" : ExpenseBy,"DoubleEntry" : iCheckState,"LeafID" : LeafID,"InvoiceDate" :InvoiceDate2,"TDSAmount" :iTDSAmount, "ModeOfPayment" :iModeOfPayment, "RowID" :rowid, "ReconcileDate" :reconcileDate, "ReconcileStatus" :reconcileStatus, "Reconcile" :reconcile, "ReturnFlag" :returnFlag, "MultipleEntry":isMultipleEntry, "Ref":ref, "InvoiceAmount":iInvoiceAmount };
			arRowsToSubmit.push(obj);
			
			//alert(arRowsToSubmit);
		}
	}
			
	showLoader();		
	var objData = {'data' : JSON.stringify(arRowsToSubmit), "method" : 'EditPaymentDetails'}; 
	$.ajax({
			url : "ajax/ajaxPaymentDetails.php",
			type : "POST",
			data: objData ,
			success : function(data)
			{	
				var arr = Array();
				arr		= data.split("@@@");		
				if(arr[1] > 0 )
				{
					alert("Record Updated Successfully...");
					var out = document.getElementById('edit').value=arr[1];
					if(out != "")
					{
						localStorage.setItem('refreshPage', "1");
						var sURL = "PaymentDetails.php?bankid=" + PayerBank + "&LeafID=" + LeafID + "&CustomLeaf=" + CustomLeaf+"&edt="+out;
						//alert(sURL);
						window.location.href = sURL;
						//window.close();				
					}
				}
				else
				{
					alert("Record Not Updated..");	
				}
 			    
				
				
				for(var c = 0; c < counterArray.length; c++)
				{
					RowCounter = counterArray[c];
					
					document.getElementById('ModeOfPayment'+ RowCounter).disabled = true;
					document.getElementById('ModeOfPayment'+ RowCounter).style.backgroundColor = 'lightgray';
					
					document.getElementById('PaidTo'+ RowCounter).disabled = true;
					document.getElementById('PaidTo'+ RowCounter).style.backgroundColor = 'lightgray';
								
					document.getElementById('ChequeNumber'+ RowCounter).disabled = true;
					document.getElementById('ChequeNumber'+ RowCounter).style.backgroundColor = 'lightgray';
					
					document.getElementById('ChequeDate'+ RowCounter).disabled = true;
					document.getElementById('ChequeDate'+ RowCounter).style.backgroundColor = 'lightgray';
					
					//document.getElementById('InvoiceDate'+ RowCounter).disabled = true;
					//document.getElementById('InvoiceDate'+ RowCounter).style.backgroundColor = 'lightgray';
					
					document.getElementById('Amount'+ RowCounter).disabled = true;
					document.getElementById('Amount'+ RowCounter).style.backgroundColor = 'lightgray';
					
					document.getElementById('VoucherCounter'+ RowCounter).disabled = true;
					document.getElementById('VoucherCounter'+ RowCounter).style.backgroundColor = 'lightgray';
					
					//document.getElementById('TDSAmount'+ RowCounter).disabled = true;
					//document.getElementById('TDSAmount'+ RowCounter).style.backgroundColor = 'lightgray';
					
					document.getElementById('Comment'+ RowCounter).disabled = true;
					document.getElementById('Comment'+ RowCounter).style.backgroundColor = 'lightgray';
					
					//document.getElementById('ExpenseTo'+ RowCounter).disabled = true;
					//document.getElementById('ExpenseTo'+ RowCounter).style.backgroundColor = 'lightgray';
					
					document.getElementById('DE' + RowCounter).disabled=true;
					document.getElementById('Edit' + RowCounter).innerHTML="<a onClick='enableRow("+ RowCounter+");'><img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;'/></a> ";    
				}
							
			//location.reload(true);	
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
	//location.reload(true);
	//}
}


function enableRow(Row)
	{ 
		if(document.getElementById('ClearVoucherNo'+ Row) != null)
		{
			ValueChanged(true,Row);
			return;
		}
		else if(document.getElementById('show_in_jvformat'+ Row).value == 1)
		{
			var voucherno = document.getElementById('PaymentVoucherNo'+ Row).value;
			var bankid = document.getElementById('bankid').value;
			window.open("VoucherEdit.php?Vno="+voucherno+"&bankid="+bankid+"&payment="+1);
			return
		}
		document.getElementById('Submit').style.display = "none";
		document.getElementById('SubmitUp').style.display = "none";
			
		if(document.getElementById('reconcileStatus' + Row).value == 0)
		{		
			document.getElementById('ChequeNumber'+ Row).disabled = false;
			document.getElementById('ChequeNumber'+ Row).style.backgroundColor = 'white';	
			
			document.getElementById('ChequeDate'+ Row).disabled = false;
			document.getElementById('ChequeDate'+ Row).style.backgroundColor = 'white';
			
			document.getElementById('Amount'+ Row).disabled = false;
			document.getElementById('Amount'+ Row).style.backgroundColor = 'white';	
		}
		
		document.getElementById('PaidTo'+ Row).disabled = false;
		document.getElementById('PaidTo'+ Row).style.backgroundColor = 'white';
		
		var EnableCounter = document.getElementById('CounterEnable').value;
		
		if(EnableCounter == 1)
		{
			document.getElementById('VoucherCounter'+ Row).disabled = false;
			document.getElementById('VoucherCounter'+ Row).style.backgroundColor = 'white';	
		}
				
		
		document.getElementById('PaidTo'+ Row).style.backgroundColor = 'white';
		
		if(document.getElementById('DE'+ Row).checked == true)
		{
		document.getElementById("DE" + Row).disabled = false;
		document.getElementById("DE" + Row).style.backgroundColor = 'white';
				
		}
	else
		{
			document.getElementById("DE" + Row).disabled = false;
			document.getElementById("DE" + Row).style.backgroundColor = 'white';
		
		}		
			
			document.getElementById('ModeOfPayment' + Row).disabled = false;
			document.getElementById('ModeOfPayment' + Row).style.backgroundColor = 'white';	
			
			document.getElementById('Comment' + Row).disabled = false;
			document.getElementById('Comment' + Row).style.backgroundColor = 'white';	
		
		document.getElementById('Edit' + Row).innerHTML="<button  id='Submit"+ Row +"' style='color: #fff;background-color: #337ab7;border-color: #2e6da4;padding: 2px 6px;font-size: 12px; border-radius: 4px;'>Submit</button><br><button  id='Cancel"+ Row +"' style='color: #fff;background-color: #337ab7;border-color: #2e6da4;padding: 2px 6px;font-size: 12px; border-radius: 4px;margin-top: 5px;' onclick='cancel("+Row+")'>Cancel</button> ";
		document.getElementById('Submit'+ Row).onclick = function () { EditChequeDetails(Row ,minGlobalCurrentYearStartDate ,maxGlobalCurrentYearEndDate);
		
	};				
		var row =document.getElementById('row'+Row);		
		var val = $(row).closest('tr').next('tr').attr('id');		
		if(val)
		{
			var id = val.substring(3);		
			if(document.getElementById('ref'+id) != null)
			{
				if(document.getElementById('ref'+id).value == document.getElementById('ref'+Row).value)
				{		
					enableRow(id);
				}
			}
		}		
	}
function cancel (Row)
 {
 //  alert(Row);
 	document.getElementById('ModeOfPayment'+ Row).disabled = true;
	document.getElementById('ModeOfPayment'+ Row).style.backgroundColor = 'lightgray';
	document.getElementById('ChequeNumber'+ Row).disabled = true;
	document.getElementById('ChequeNumber'+ Row).style.backgroundColor = 'lightgray';
	var EnableCounter = document.getElementById('CounterEnable').value;
	if(EnableCounter == 1)
	{
		document.getElementById('VoucherCounter'+ Row).disabled = true;
		document.getElementById('VoucherCounter'+ Row).style.backgroundColor = 'lightgray';	
	}
	document.getElementById('ChequeDate'+ Row).disabled = true;
	document.getElementById('ChequeDate'+ Row).style.backgroundColor = 'lightgray';
	document.getElementById('PaidTo'+ Row).disabled = true;
	document.getElementById('PaidTo'+ Row).style.backgroundColor = 'lightgray';	
	document.getElementById('Amount'+ Row).disabled = true;
	document.getElementById('Amount'+ Row).style.backgroundColor = 'lightgray';	
	document.getElementById('Comment'+ Row).disabled = true;
	document.getElementById('Comment'+ Row).style.backgroundColor = 'lightgray';	
	document.getElementById('Submit'+ Row).style.display = "none";
	document.getElementById('Cancel' + Row).style.display = "none";
	document.getElementById('Edit' + Row).innerHTML="<a onClick='enableRow("+ Row+");'><img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;'/></a> "; 
}
function timeout(field)
{
	document.getElementById(field.id).innerHTML = "";
}

function setLedArray(flag)
{			
	$.ajax({
			url : "ajax/ajaxPaymentDetails.php",
			type : "POST",
			data: {"method" : "fetchLedgers"} ,
			success : function(data)
			{	
				getArray(data,flag);
			}
		});																			
}

function getArray(data,flag)
{	
	var a1		= data;
	var newArr1	= new Array();																
	newArr1		= a1.split("@@@");	
	newArr = newArr1[1];
	if(flag == true)
	{
		var obj = {"method" : "fetchExpenseBy"};
		remoteCallNew("ajax/ajaxPaymentDetails.php", obj, 'getExpenseByArr');
	}
}

function getExpenseByArr()
{
	var a		= sResponse.trim();
	var newArr	= new Array();																
	newArr		= a.split("@@@");	
	ExpenseByArr = newArr[1];		
	getExistingData(aryChqNo, leafID, customleaf);	
}

function createCouterArray(cnt)
{					
	var row =document.getElementById('row'+cnt);		
	var val = $(row).closest('tr').next('tr').attr('id');
	if(val == null || val == "RowLabel"+cnt)
	{
		val = $(row).closest('tr').next('tr').next('tr').attr('id');
	}
		
	if(val)
	{
		var id = val.substring(3);				
		if(document.getElementById('ref'+id) != null)
		{			
			if(document.getElementById('ref'+id).value == document.getElementById('ref'+cnt).value)
			{			
				counterArray.push(id);
				createCouterArray(id);
			}
		}
	}	
}

function FindValueInArray(arr, obj) 
{
    for(var i=0; i<arr.length; i++) 
	{
        if (arr[i] == obj) return true;
    }
	return false;
}

function hideEditBtn()
{
	var EditBtnList = document.getElementsByClassName('btnEdit'), i;

	for (var i = 0; i < EditBtnList.length; i ++) {
		EditBtnList[i].style.display = 'none';
	}	
	document.getElementById('lblEdit').style.display = 'none';
}


function SubmitEntry(RowCounter)
{      
		
   
   		var SubmitValue=1; 
		var arSubmitEntry = [];
		var PaidTo = document.getElementById('Paidto').value;
		//alert("Paid To :"+PaidTo);
		var ChequeNumber = document.getElementById('cheque_no').value;
		//alert(" ChequeNumber :"+ ChequeNumber);
		var ChequeDate = document.getElementById('cheque_date').value;
		//alert(" ChequeDate :"+ ChequeDate);
		var ChequeAmount = document.getElementById('chequeamouunt').value;
		//alert("  ChequeAmount :"+  ChequeAmount);
		var LeafID = document.getElementById('LeafID').value;
		//alert(" LeafID :"+  LeafID);
		var PayerBank = document.getElementById('bankid').value;
		//alert(" PayerBank :"+ PayerBank);
		var Comments = document.getElementById('Note').value;
		//alert(" Comments:"+Comments);
		var ClearVoucherNo = document.getElementById('ClearVoucherNo').value;
		///alert(" ClearVoucherNo:"+ClearVoucherNo);
		var ModeOfPayment = document.getElementById('ModeOfPayment').value;
		//alert(" ModeOfPayment:"+ModeOfPayment);
		var recStatus = document.getElementById('recStatus').value;
		//alert(" recStatus:"+recStatus);
		var reconcileDate = document.getElementById('reconcileDate').value;
		//alert(" reconcileDate:"+reconcileDate);
		var reconcile = document.getElementById('reconcile').value;
		//alert(" reconcile:"+reconcile);
		var ExistPaymentVoucher = document.getElementById('ExistPaymentVoucher').value;
		//alert(" ExistPaymentVoucher:"+ExistPaymentVoucher);
		//var InputSGST = document.getElementById('InputSGST').value;
		//var InputCGST = document.getElementById('InputCGST').value;
		//alert(ModeOfPayment);
		//alert(FieldCount);
		var invoiceArray=[];
		var deleteinvoiceVoucherArray =[];
		//console.log('Field Log '+FieldCount);
		var cnt = 0;
		for(var i=1;i<=FieldCount;i++)
		{
			if(document.getElementById('select_invoice_' + i) == null || document.getElementById('select_invoice_' + i) == undefined)
			{
				continue;
			}
			
			var chkState = document.getElementById('select_invoice_' + i).checked;
			var DeleteInvoice = document.getElementById('delete_'+i).checked;
			
			if(DeleteInvoice == true)
			{
				deleteinvoiceVoucherArray[cnt] = [];
				var voucherNumber = document.getElementById('voucher_no_'+i).value; 
				var ClearVoucherNo = document.getElementById('ClearVoucherNo').value; 
				var BankID = document.getElementById('bankid').value; 
				var LeafID = document.getElementById('LeafID').value;
				deleteinvoiceVoucherArray[cnt] =  {'voucherNumber' : voucherNumber}
				cnt++;
			}
			
			if(chkState==true)
			{
				var iInvoiceCounter = invoiceArray.length;
				invoiceArray[iInvoiceCounter] = [];
				var InvoiceDate = document.getElementById('invoice_date_'+ i).value;
				//alert(" InvoiceDate:"+InvoiceDate);
				var InvoiceNumber = document.getElementById('invoice_no_'+ i).value;
				//alert(" InvoiceNumber:"+InvoiceNumber);
				var ExpenceBy = document.getElementById('Expence_by_'+ i).value;
				//alert(" ExistPaymentVoucher:"+ExistPaymentVoucher);
				var InvoiceAmount = document.getElementById('invoice_amount_'+ i).value;
				//alert(" InvoiceAmount:"+InvoiceAmount);
				var TDSAmount = document.getElementById('TDS_amount_'+i).value;
				//alert(" TDSAmount:"+TDSAmount);
				//var IGSTAmount = document.getElementById('IGST_amount_'+i).value;
				var CGSTAmount = document.getElementById('CGST_amount_'+i).value;
				//alert(" CGSTAmount:"+CGSTAmount);
				var SGSTAmount = document.getElementById('SGST_amount_'+i).value;
				//alert(" SGSTAmount:"+SGSTAmount);
				//var CESSAmount = document.getElementById('CESS_amount_'+i).value;
				var TDSPayable = document.getElementById('TDS_Payable_'+i).value;
				//alert(" TDSPayable:"+TDSPayable);
				var IsInvoice = document.getElementById('is_invoice'+i).value;
				//alert(" IsInvoice:"+IsInvoice);
				var GrossAmount = document.getElementById('gross_amount_'+i).value;
				//alert(" GrossAmount:"+GrossAmount);
				//var InputSGST = document.getElementById('Input_SGST_'+i).value;
				//var InputCGST = document.getElementById('Input_CGST_'+i).value;
				var DocStatusID = document.getElementById('Doc_statusID_'+i).value;
				//alert(" DocStatusID:"+DocStatusID);
				var NewInvoice = document.getElementById('New_Invoice_'+i).value;
				//alert(" NewInvoice:"+NewInvoice);
				//alert(InputSGST);
				//alert(InputCGST);
				if(InvoiceDate=='')
				{
					alert("Please Select Invoice Date!");
			 		return false;
				}

				if(jsdateValidator('invoice_date_'+ i, InvoiceDate , minGlobalCurrentYearStartDate , 					                 maxGlobalCurrentYearEndDate)==false){
					return false;
					}
	
					
				if(InvoiceNumber=='')
				{
					alert("Please Enter Invoice Number!");
			 		return false;
				}
				if(ExpenceBy == '0')
				{
					alert("Please Select Expense By!");
			 		return false;
				}
				//if(GrossAmount=='')
				//{
					//alert("Please Enter Gross Amount!");
			 		//return false;
				//}
				if(InvoiceAmount=='')
				{
					alert("Please Enter Invoice Amount!");
			 		return false;
				}
				var txt;
   
				invoiceArray[iInvoiceCounter] = {'InvoiceDate' : InvoiceDate,
									'InvoiceNumber' : InvoiceNumber,
									'ExpenceBy': ExpenceBy,
									'InvoiceAmount' : InvoiceAmount,
									'TDSAmount': TDSAmount,
									//'IGSTAmount': IGSTAmount,
									'CGSTAmount': CGSTAmount,
									'SGSTAmount': SGSTAmount,
									//'CESSAmount': CESSAmount,
									'InvoiceStatusID': DocStatusID,
									'TDSPayable': TDSPayable,
									'IsInvoice' :IsInvoice,
									'NewInvoice':NewInvoice}
									//'InputSGST' : InputSGST,
									//'InputCGST' : InputCGST};
			}
			else 
			{  
				
			}

		}
		 if (confirm("Are you sure you want to submit the entry ?") == true)
		  {
		  } 
		else 
		 {
      		return false;
    	 }
		var obj = ""; 	 
			obj = {"PaidTo" : PaidTo, "ChequeNumber" : ChequeNumber, "ChequeDate" : ChequeDate, "Amount" : ChequeAmount, "PayerBank" : PayerBank, "Comments" : Comments,"LeafID" : LeafID, "VoucherDate" : ChequeDate,"Ref" :0,"MultipleEntry":0,"ClearVoucherNo":ClearVoucherNo,"ModeOfPayment":ModeOfPayment,"recStatus":recStatus, "reconcileDate":reconcileDate,"reconcile":reconcile, "ExistPaymentVoucher" : ExistPaymentVoucher,  "Invoices" : JSON.stringify(invoiceArray), "deleteInvoicesVoucher" : JSON.stringify(deleteinvoiceVoucherArray),"popupPayment": SubmitValue };
		arSubmitEntry.push(obj);
		
		showLoader();		
		var objData = {'data' : JSON.stringify(arSubmitEntry), "method" : 'AddPaymentDetails'}; 
		console.log("My Data : " + objData['data']);
		//return;
		$.ajax({ 
			url : "ajax/ajaxPaymentDetails.php",
			type : "POST",
			data: objData ,
			success : function(data)
			{	
				var arr = Array();
				arr		= data.split("@@@");
				//alert(data);			
				if(arr[1]==true)
				{
					alert("Record Updated Successfully...");
					window.close();
					window.opener.location.reload();
					
				}
				else if(arr[1]== false)
				{
					alert("Record not updated, Cheque number already used in another Cheque Leaf !");	
				}
				else
				{
					alert("Record not updated ...")
				}
			}
		
	
		
	});
   }
   


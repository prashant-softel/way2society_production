


function CheckAllBankChkbox()
{

	var chkboxAllBnk  = document.getElementById('CheckAll').checked;
	var numberOfBank = document.getElementById('NumberOfBank').value;
	console.log(numberOfBank);
	if(chkboxAllBnk == true)
	{
		console.log('after condition');

	 	for(var rowCnt = 0; rowCnt < numberOfBank; rowCnt++)
		{
				//alert('Bingo');
			document.getElementById('BankReceipt'+rowCnt).checked = true;
			document.getElementById('BankPayment'+rowCnt).checked = true;
		}	
	}
	else
	{
		for(var rowCnt =0; rowCnt < numberOfBank ; rowCnt++)
		{
			//alert('Bingo1');
			document.getElementById('BankReceipt'+rowCnt).checked = false;
			document.getElementById('BankPayment'+rowCnt).checked = false;
		}
	
	} 
}

















function getvoucherCounter(str)
{
	var iden	= new Array();
	iden		= str.split("-");

	$('html, body').animate({ scrollTop: $('#top').offset().top }, 300);

	if(iden[0]=="delete")
	{
		var conf = confirm("Are you sure , you want to delete it ???");
		if(conf==1)
		{
			$(document).ready(function()
			{
				$("#error").fadeIn("slow");
				document.getElementById("error").innerHTML = "Please Wait...";
			});

			remoteCall("../ajax/voucherCounter.ajax.php","form=voucherCounter&method="+iden[0]+"&voucherCounterId="+iden[1],"loadchanges");
		}
	}
	else
	{
		$(document).ready(function()
		{
			$("#error").fadeIn("slow");
			document.getElementById("error").innerHTML = "Please Wait...";
		});

		remoteCall("../ajax/voucherCounter.ajax.php","form=voucherCounter&method="+iden[0]+"&voucherCounterId="+iden[1],"loadchanges");
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
		$(document).ready(function()
		{
			$("#error").fadeIn("slow");
			document.getElementById("error").innerHTML = "Please Wait...";
		});

		document.getElementById('JournalVoucher').value=arr2[1];
		document.getElementById('CashPay').value=arr2[2];
		document.getElementById('CashReceive').value=arr2[3];
		document.getElementById('BankPayment').value=arr2[4];
		document.getElementById('BankReceipt').value=arr2[5];
		document.getElementById("id").value=arr2[0];
		document.getElementById("insert").value="Update";
	}
	else if(arr1[0] == "delete")
	{
		$(document).ready(function()
		{
			$("#error").fadeIn("slow");
			document.getElementById("error").innerHTML = "Please Wait...";
		});

		window.location.href ="../voucherCounter.php?mst&"+arr1[1]+"&mm";
	}
}

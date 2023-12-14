function view(id)
{	
//exp_group_id	
	/*if(id == 8)
	{
	 document.getElementById("exp_group").disabled= false;
	}
	else
	{
		 document.getElementById("exp_group").disabled= true;
		 document.getElementById("exp_group").checked = false;
	}*/
	if(id != 0)
	{
	document.getElementById("tbl_showheader").style.display = "none";
	 	document.getElementById("tbl_showheader1").style.display = "block";
	 	
	 document.getElementById("ledgerfetch").style.display = "table-row";
	 var select = document.getElementById("selectedledger");
		for (var option in select){
			select.remove(option);
		}
	$.ajax({
		url : "ajax/multiple_ledger_print.ajax.php",
		type : "POST",
		datatype: "JSON",
		data : {"method":"Fetch","gId":id},
		success : function(data)
		{	
		
		var fetchdata = data.split("@@@");
		var arr4 = JSON.parse("["+fetchdata[1]+"]");
		document.getElementById("ledger")
		var main = document.getElementById("ledger"); 
		
	var select = document.getElementById("ledger");
		for (var option in select){
			select.remove(option);
		}
		for(var i=0;i< arr4[0].length;i++)
		{
			
			var arr5 = arr4[0][i];
			var option = document.createElement("option");
	                    option.value= arr5['LedID'];
	                    option.text = arr5['LedName'];
						
	        main.appendChild(option);
				
		}
		//document.getElementById('Comment'+counter).value = fetchdata[1];
		
		}
	});
	}
	else
	{
		document.getElementById("ledgerfetch").style.display="none";
		document.getElementById("tbl_showheader").style.display = "block";
			document.getElementById("tbl_showheader1").style.display = "none";
	 
	 
    }

}
function uncheckDefaultCheckBox(id)
{
  if(document.getElementById(id).checked  == true)
  {
    document.getElementsByClassName('checkBox')[0].checked = false;
  }
  else
  {
    document.getElementsByClassName('checkBox')[0].checked = true;
  }
}	

function fetchledgerdata()
{
	
	var checks = document.getElementsByClassName('checkBox');
			var memAttendance = [];
			checks.forEach(function(val, index, ar)
			{
				if(ar[index].checked) 
				{
			//alert ("In First if");
					if(ar[index].id != "")
					{
				//alert ("In 2 if");
						memAttendance.push(ar[index].id);
					}
					
        		}
    		});
			console.log(memAttendance);
}
function setArray()
{
	var ledgerid = new Array();	
	var ledgers = document.getElementById('selectedledger');
	var ledger_type = document.getElementById('ledger_type').value;
	if(ledger_type != 0)
	{
	if(ledgers.length == 0)
	{
		alert("Kindly Select Ledger to Display");
		return false;
	}
	else
	{
	for(i = 0; i < ledgers.length;i++)
	{
		var val = ledgers.options[i].value;
		ledgerid.push(val);
		
	}	

	document.getElementById("ledgerid").value = JSON.stringify(ledgerid);
	}
	}
}
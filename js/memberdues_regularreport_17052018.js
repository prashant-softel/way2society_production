function sendNotice()
{
	
	var sid= document.getElementById('sid').value;
	
	var Sub= document.getElementById('temp_sub').value;
	var Data= document.getElementById('temp_data').value;
	
	var sMsg = Sub+"<br />"+Data+""; 
	window.location.href = "memberdues_regularreport.php?&sid="+sid+"#openDialogYesNo";
		var sText = '<a href="#close" title="Close" class="close" id="close">X</a>';
		sText += '<h2>Confirm Operation</h2><p>' + sMsg + '</p><br/><br/>';
		sText += '<a href="#close" title="Close" class="yes" id="dialogYesNo_yes">YES</a>';
		sText += '<a href="#close" title="Close" class="no" id="dialogYesNo_no">NO</a>';
		document.getElementById('message_yesno').innerHTML = sText;
		document.getElementById("dialogYesNo_yes").onclick = function () { submitForm(true); };
		document.getElementById("dialogYesNo_no").onclick = function () { submitForm(false); };
	
	
}

function submitForm(bSubmit)
{
	if(bSubmit == true)
	{
		var UnitArray = [];
		var unitAry = document.getElementById('unit_ary').value.split('#');
		
		for(var iCnt = 0 ; iCnt < unitAry.length - 1; iCnt++)
		{
			if(document.getElementById('chk_' + unitAry[iCnt]).checked == true)
			{
					var amount = parseFloat($('#totalAmounts_' + unitAry[iCnt]).html());
			//alert("checked is: "+unitAry[iCnt]);
		         	if(amount > 0)
			      {
				UnitArray.push(unitAry[iCnt]);
			
		              }
			}
		
		}
	
	$.ajax({
					
				url : "ajax/memberdues_regularreport.ajax.php",
				type : "POST",
				data : {"method" : 'sendDuesNotice',"UnitArray" :JSON.stringify(UnitArray)},
	
		success : function(data)
				{	
					
					alert("Notice send successfully");
				
				},
			fail: function()
			{
				alert("Notice not send");
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
				alert('Notice not send');
				//alert(textStatus);
				//alert(errorThrown);
			}
			});

	}
}

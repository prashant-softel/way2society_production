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
function ShowSearchElement()
{
  var input, filter, ul, li, a, i;
    input = document.getElementById("searchbox");
    filter = input.value.toUpperCase();
    ul = document.getElementById("mem_id");
    li = ul.getElementsByTagName("li");
    for (i = 0; i < li.length; i++) {
        //a = li[i].getElementsByTagName("a")[0];
        if (li[i].innerHTML.toUpperCase().indexOf(filter) > -1) {
            li[i].style.display = "";
        } else {
            li[i].style.display = "none";
        }
    }
}
function FetchMemValue()
{
	//var ignore_zero = document.getElementById('ignore_zero').checked;
	var method=document.getElementById("create").value;
	if(method=="Create")
	{
		var checks = document.getElementsByClassName('checkBox');
		var MemArray = [];
		var gName= document.getElementById('grpname').value;
		var gDes= document.getElementById('grpdes').value;
		gName=gName.trim().replace(/ /g, '%20');
		gDes=gDes.trim().replace(/ /g, '%20');
    //alert ("Name:"+gName+"\nDes:"+gDes);
		checks.forEach(function(val, index, ar)
		{
			if(ar[index].checked) 
			{
				//alert ("In First if");
				if(ar[index].id != "")
				{
				//alert ("In 2 if");
					MemArray.push(ar[index].id);
				}
				else
				{		
				//alert ("In First else");
					MemArray = [];
					checks.forEach(function(val, index, ar) 
					{
						if(ar[index].id != "")
						{
						//alert ("In 3 if");
							MemArray.push(ar[index].id);
						}
					});
				}
        	}
    	});
		var i=0;
	//alert ("Array Length: "+MemArray.length); 
	//alert ("Mem Array:");
	/*for(i=0;i<MemArray.length;i++)
	{
		alert (i+": "+MemArray[i]);
	}*/
		if(MemArray.length == 0)
		{
		//this array is empty
			MemArray = [];
			checks.forEach(function(val, index, ar) 
			{
				if(ar[index].id != "")
				{
					MemArray.push(ar[index].id);
				}
			});
		}
		$.ajax({
			url : "ajax/createGrp.ajax.php",
			type : "POST",
			datatype: "JSON",
			data : {"method":"Fetch","memIdArray":JSON.stringify(MemArray),"gName":gName,"gDes":gDes},
			success : function(data)
			{	
				//alert ("Data:"+data);
				window.location.href ="momGroup.php";
				//hideLoader();
				//if(document.getElementById('AllowExport').value  ==  1)
				//{	
					//document.getElementById('btnExport').style.display = 'block';
					//document.getElementById('Print').style.display = 'block';
				//}
				
			}	
			/*fail: function()
			{
				
			},
			
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
			}*/
		});
	}
	else if(method=="Update")
	{
		var checks = document.getElementsByClassName('checkBox');
		var MemArray = [];
		var gId=document.getElementById("gId").value;
		var gName= document.getElementById('grpname').value;
		var gDes= document.getElementById('grpdes').value;
		gId=gId.trim().replace(/ /g,'%20');
		gName=gName.trim().replace(/ /g, '%20');
		gDes=gDes.trim().replace(/ /g, '%20');
    //alert ("Name:"+gName+"\nDes:"+gDes);
		checks.forEach(function(val, index, ar)
		{
			if(ar[index].checked) 
			{
			//alert ("In First if");
				if(ar[index].id != "")
				{
				//alert ("In 2 if");
					MemArray.push(ar[index].id);
				}
				else
				{		
				//alert ("In First else");
					MemArray = [];
					checks.forEach(function(val, index, ar) 
					{
						if(ar[index].id != "")
						{
						//alert ("In 3 if");
							MemArray.push(ar[index].id);
						}
					});
				}
        	}
    	});
		var i=0;
	
		if(MemArray.length == 0)
		{
		//this array is empty
			MemArray = [];
			checks.forEach(function(val, index, ar) 
			{
				if(ar[index].id != "")
				{
					MemArray.push(ar[index].id);
				}
			});
		}
		$.ajax({
			url : "ajax/createGrp.ajax.php",
			type : "POST",
			datatype: "JSON",
			data : {"method":"Update","memIdArray":JSON.stringify(MemArray),"gId":gId,"gName":gName,"gDes":gDes},
			success : function(data)
			{	
				//alert ("Data:"+data);
				window.location.href ="momGroup.php";
				//hideLoader();
				//if(document.getElementById('AllowExport').value  ==  1)
				//{	
					//document.getElementById('btnExport').style.display = 'block';
					//document.getElementById('Print').style.display = 'block';
				//}
				
			}	
		});
	}
}

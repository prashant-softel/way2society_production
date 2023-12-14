// JavaScript Document

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
function FetchAgendaDetails()
{
	var mId=document.getElementById("mId").value;
	var mRow=document.getElementById("maxrows").value;
	var i;
	var id=[];
	var agenda=[];
	var ag;
	var minutes=[];
	var mint;
	var resolution=[];
	var res;
	var pId=[];
	var sId=[];
	var passBy=[];
	var srNo=[];
	var note;
	var eNote;
	for(i=0;i<mRow;i++)
	{
		id[i]=document.getElementById("id"+i).value;
		srNo[i]=document.getElementById("srNo"+i).value;
		ag=document.getElementById("agenda"+i).value;
		ag=encodeURI(ag);
		agenda[i]=ag;
		ag=" ";
		mint=document.getElementById("minutes"+i).value;
		mint=encodeURI(mint);
		minutes[i]=mint;
		mint=" ";
		res=document.getElementById("resolution"+i).value;
		res=encodeURI(res);
		resolution[i]=res;
		res=" ";
		pId[i]=(document.getElementById("pName"+i).value).trim().replace(/ /g, '%20');
		sId[i]=(document.getElementById("sName"+i).value).trim().replace(/ /g, '%20');
		passBy[i]=(document.getElementById("passBy"+i).value).trim().replace(/ /g, '%20');
	}
	var endText=document.getElementById('endNote').value;
	endText=encodeURI(endText);
	//alert ("len:"+id.length);
	var checks = document.getElementsByClassName('checkBox');
	var MemArray = [];
	var type = document.getElementById("create").value;
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
	var note=document.getElementById("note").value;
	var attendance = " "+MemArray.length;
	var indexOfComma = getPosition(note,',',3);
	note = [note.slice(0, indexOfComma+1), attendance, note.slice(indexOfComma+1)].join('');
	//alert ("indexOfOb :"+note);
	note=encodeURI(note);
	$.ajax
	({
		url : "ajax/minutesOfMeeting.ajax.php",
		type : "POST",
		datatype: "JSON",
		data : {"method":"Update","mId":mId,"Id":JSON.stringify(id),"minutes":JSON.stringify(minutes),"res":JSON.stringify(resolution),"pId":JSON.stringify(pId),"sId":JSON.stringify(sId),"passBy":JSON.stringify(passBy),"note":note,"endNote":endText,"memIdArray":JSON.stringify(MemArray),"type":type},
		success : function(data)
		{	
			//alert ("Data:"+data);
			document.getElementById("create").style.display="none";
			document.getElementById("preview").style.display="table-cell";
		}
	});	
}
function getPosition(string, subString, index) {
   return string.split(subString, index).join(subString).length;
}
function PreviewDetails()
{
	var mId=document.getElementById("mId").value;
	document.getElementById("cbHeader").style.display="table-cell";
	if(mId!=0)
	{
		document.getElementById("preview_table").style.display="table-row";
		$.ajax({
			url : "ajax/minutesOfMeeting.ajax.php",
			type : "POST",
			datatype: "JSON",
			data : {"method":"FetchPreview","mId":mId},
			success : function(data)
			{
				CKEDITOR.instances['events_desc'].setData(data);
				document.getElementById("finalRow").style.display="table-row";	
			}
		});
	}
}
function finaliseMethod()
{
	var mId=document.getElementById("mId").value;
	$.ajax
	({
		url : "ajax/minutesOfMeeting.ajax.php",
		type : "POST",
		datatype: "JSON",
		data : {"method":"final","mId":mId},
		success : function(data)
		{
			//alert ("data:"+data);
			window.location.href="meeting.php?type=closed";
		}
	});
}
var checked_count=1;
var ckOriginal_data="";
function addHeader()
{
	
	if(checked_count % 2 != 0)
	{
		ckOriginal_data=CKEDITOR.instances['events_desc'].getData();
	}
	if(document.getElementById('socHeader').checked == true)
	{
		$.ajax
		({
			url : "ajax/minutesOfMeeting.ajax.php",
			type : "POST",
			datatype: "JSON",
			data : {"method":"getHeader"},
			success : function(data)
			{	
				//alert("Data:"+data);
				CKEDITOR.instances['events_desc'].setData(data+""+ckOriginal_data);
				//document.getElementById("finalRow").style.display="table-row";
				//window.location.href ="meeting.php?type=open";
			}
		});
	}
	else if(document.getElementById('socHeader').checked == false)
	{
		CKEDITOR.instances['events_desc'].setData(ckOriginal_data);
	}
	checked_count++;
}
function ExpandCollpase()
{
	var method = document.getElementById("btnExCol").value;
	var maxRows = parseInt(document.getElementById("maxrows").value);
	//alert ("max : "+maxRows)
	var i =0,j=0;
	if(method == "Expand")
	{
		for(i=0;i<maxRows;i++)
		{
			for(j=1;j<7;j++)
			{
				document.getElementById("hide"+j+"A"+i).style.display = "table-cell";
			}
		}
		document.getElementById("btnExCol").value = "Collapse";
	}
	else
	{
		for(i=0;i<maxRows;i++)
		{
			for(j=1;j<7;j++)
			{
				document.getElementById("hide"+j+"A"+i).style.display = "none";
			}
		}
		document.getElementById("btnExCol").value = "Expand";
	}
}
function for_print()
{
	
	var html = CKEDITOR.instances.events_desc.getSnapshot();
	var print_div = document.getElementById('for_printing');
	print_div.innerHTML = html;			
	var mywindow = window.open('', 'PRINT', 'height=600,width=800');
	mywindow.document.write('<html><head><title></title>');
    mywindow.document.write('</head><body>');
    mywindow.document.write(document.getElementById('for_printing').innerHTML);
	mywindow.document.write('</body></html>');
	mywindow.document.close(); // necessary for IE >= 10
	mywindow.focus(); // necessary for IE >= 10*/
	mywindow.print();
	mywindow.close();
	return false;
}
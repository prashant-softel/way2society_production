function addNewRow()
{
	var i =0,lastI = 0,j=1;
	//iCounter=parseInt(document.getElementById("maxrows").value);
	var maxlength = parseInt(document.getElementById("maxlength").value);
	//alert("counter:"+iCounter);
	sContent="<tr style='padding-top:1%;padding-bottom:10%' id='agendaRow"+maxlength+"'><td width='30%' style='text-align:left;padding-left:15%'><b><font style='color:#F00'>*</font>Agenda:</b></td>";
   	sContent+="<td width='5%'><input type='text' id='srNo"+maxlength+"' name='srNo"+maxlength+"' placeholder='Sr. No.' style='width:40px;vertical-align:top' /><br><br><br><br><a id='del' onClick='deleteRow("+maxlength+")' style='width:70%;height:120%;'><img src='images/del.gif' alt='Delete' style='width:70%;height:120%;padding-left:30%'></img></a><input type='hidden' id='aId"+maxlength+"' name='aId"+maxlength+"'/></td>";   
	sContent+="<td width='65%'><textarea id='agenda"+maxlength+"' name='agenda"+maxlength+"' cols='90' rows='6' placeholder='Enter the meeting agenda here' style='margin-left:1%'></textarea></td></tr>";
	$("#agenda > tbody").append(sContent);
	for(i = 0;i<=maxlength;i++)
	{
		var checkId = document.getElementById("srNo"+i);
		if(checkId)
		{
			document.getElementById("srNo"+i).value = j;
			j = j + 1;
		}
	}
	//alert ("lastI"+lastI);
	//document.getElementById("srNo"+iCounter).value = parseInt(lastI)+1;
	document.getElementById("srNo"+maxlength).readOnly = "true";
	maxlength = maxlength + 1;	
	document.getElementById('maxrows').value = maxlength;
	document.getElementById('maxlength').value = maxlength;
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
function view(id)
{		
	document.getElementById("members1").style.display="table-cell";
	document.getElementById("members2").style.display="table-cell";
	document.getElementById("members3").style.display="table-cell";
	//document.getElementById("members4").style.display="table-cell";
	//alert ("GId:"+id);
	$.ajax({
		url : "ajax/createMeeting.ajax.php",
		type : "POST",
		datatype: "JSON",
		data : {"method":"Fetch","gId":id},
		success : function(data)
		{	
			document.getElementById("mem_id").innerHTML=data;
		}
	});
}	
function loadchanges(data)
{
	//alert ("Data:"+data);
	document.getElementById("mem_id").innerHTML=data;
}

function FetchMeetingDetails()
{
	var method=document.getElementById("create").value;
	if(method=="Create")
	{
		//For meeting Table..
		var title=document.getElementById("title").value;
		//title=title.trim().replace(/ /g,'%20');
		title=encodeURI(title);
		var mDate=document.getElementById("mdate").value;
		mDate=mDate.trim().replace(/ /g,'%20');
		var hr=document.getElementById('hr').value;
		var mn=document.getElementById('mn').value;
		//alert("mn:"+mn);
		var ampm=document.getElementById('ampm').value;
		time = hr+":"+mn+ampm;
		//alert ("time:"+time);
		time=time.trim().replace(/ /g,'%20');
		var venue=document.getElementById("venue").value;
		//venue=venue.trim().replace(/ /g,'%20');
		venue=encodeURI(venue);
		var mLastDate=document.getElementById("mlastdate").value;
		mLastDate=encodeURI(mLastDate);
		var notes=document.getElementById("notes").value;
		//notes=notes.trim().replace(/ /g,'%20');
		notes=encodeURI(notes);
		var endText=document.getElementById("endText").value;
		//endText=endText.trim().replace(/ /g,'%20');
		endText=encodeURI(endText);


		//For MeetingAgenda Table
		var maxRow=document.getElementById("maxrows").value;
		var i,ii=0;
		var a1, s1,a,s;
		var agenda=[];
		var srNo=[];
		var aId;
		for(i=0;i<maxRow;i++)
		{
			a=document.getElementById("srNo"+i);
			if(a)
			{
				a1 = document.getElementById("agenda"+i).value;
				a1=a1.trim().replace(/ /g,'%20');
				agenda[ii]=a1;
				s1=document.getElementById("srNo"+i).value;
				s1=s1.trim().replace(/ /g,'%20');
				srNo[ii]=s1;
				ii=ii+1;
			}
			else
			{
				continue;
			}
		}
		var gId=document.getElementById("grpname").value;
		//alert ("GId:"+gId);
		if(gId=="")
		{
			alert("Please select the invitees..");
		}
		else
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
					else
					{		
				//alert ("In First else");
						memAttendance = [];
						checks.forEach(function(val, index, ar) 
						{
							if(ar[index].id != "")
							{
						//alert ("In 3 if");
								memAttendance.push(ar[index].id);
							}
						});
					}
        		}
    		});
			var i=0;
			if(memAttendance.length == 0)
			{
		//this array is empty
				memAttendance = [];
				checks.forEach(function(val, index, ar) 
				{
					if(ar[index].id != "")
					{
						memAttendance.push(ar[index].id);
					}
				});
			}
		//alert ("A:"+agenda[1]);
		//alert("No:"+srNo[1]);
			$.ajax
			({
				url : "ajax/createMeeting.ajax.php",
				type : "POST",
				datatype: "JSON",
				data : {"method":"Create","title":title,"mDate":mDate,"mTime":time,"venue":venue,"mLastDate":mLastDate,"notes":notes,"eText":endText,"gId":gId,"SrNo":JSON.stringify(srNo),"agendaArr":JSON.stringify(agenda), "memArr":JSON.stringify(memAttendance)},
				success : function(data)
				{	
					document.getElementById("preview").style.display="table-cell";
					document.getElementById("create").style.display="none";
					$.ajax
					({
						url : "ajax/createMeeting.ajax.php",
						type: "POST",
						datatype: "JSON",
						data : {"method":"getMeetingId"},
						success : function(data1)
						{	
							//alert ("mId"+data1);
							var mId=data1;
							document.getElementById("mId").value=mId;
						}
					});
				}	
			});
		}
	}
	if(method=="Update")
	{
		var id=document.getElementById("mId").value;
		id=id.trim().replace(/ /g,'%20');
		//alert("Id:"+Id);
		var title=document.getElementById("title").value;
		//title=title.trim().replace(/ /g,'%20');
		title=encodeURI(title);
		var mDate=document.getElementById("mdate").value;
		mDate=mDate.trim().replace(/ /g,'%20');
		var hr=document.getElementById('hr').value;
		var mn=document.getElementById('mn').value;
		var ampm=document.getElementById('ampm').value;
		time = hr+":"+mn+ampm;
		var venue=document.getElementById("venue").value;
		//venue=venue.trim().replace(/ /g,'%20');
		venue=encodeURI(venue);
		var mLastDate=document.getElementById("mlastdate").value;
		mLastDate=encodeURI(mLastDate);
		var notes=document.getElementById("notes").value;
		//notes=notes.trim().replace(/ /g,'%20');
		notes=encodeURI(notes);
		var endText=document.getElementById("endText").value;
		//endText=endText.trim().replace(/ /g,'%20');
		endText=encodeURI(endText);
		
		//For MeetingAgenda Table
		var maxRow=document.getElementById("maxrows").value;
		//alert ("Max:"+maxRow);
		var i,ii=0;
		var a, s, id;
		var agenda=[];
		var srNo=[];
		var aId=[];
		for(i=0;i<maxRow;i++)
		{
			a=document.getElementById("agenda"+i).value;
			if(a=="")
			{
				
			}
			else
			{
				a=a.trim().replace(/ /g,'%20');
				agenda[ii]=a;
				s=document.getElementById("srNo"+i).value;
				s=s.trim().replace(/ /g,'%20');
				srNo[ii]=s;
				id=document.getElementById("aId"+i).value;
				aId[ii]=id;
				ii=ii+1;
			}
		}
		var gId=document.getElementById("grpname").value;
		//alert ("GId:"+gId);
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
				else
				{		
				//alert ("In First else");
					memAttendance = [];
					checks.forEach(function(val, index, ar) 
					{
						if(ar[index].id != "")
						{
						//alert ("In 3 if");
							memAttendance.push(ar[index].id);
						}
					});
				}
        	}	
    	});
		var i=0;
		if(memAttendance.length == 0)
		{
		//this array is empty
			memAttendance = [];
			checks.forEach(function(val, index, ar) 
			{
				if(ar[index].id != "")
				{
					memAttendance.push(ar[index].id);
				}
			});
		}
		$.ajax({
			url : "ajax/createMeeting.ajax.php",
			type : "POST",
			datatype: "JSON",
			data : {"method":"Update","id":id,"title":title,"mDate":mDate,"mTime":time,"venue":venue,"mLastDate":mLastDate,"notes":notes,"eText":endText,"gId":gId,"aId":JSON.stringify(aId),"SrNo":JSON.stringify(srNo),"agendaArr":JSON.stringify(agenda), "memArr":JSON.stringify(memAttendance)},
			success : function(data)
			{	
				document.getElementById("preview").style.display="table-cell";
				//alert ("Data:"+data);
				//window.location.href ="meeting.php?type=open";
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
function viewNote()
{
	var title=document.getElementById("title").value;
	var mDate=document.getElementById("mdate").value;
	mDate=mDate.trim().replace(/ /g,'%20');
	var date=mDate.substr(0,2);
	var ssup,d;
	d=date.substr(1,1);
	if(d=="2")
	{
		ssup="nd";
	}
	else if(d=="3")
	{
		ssup="rd";
	}
	else
	{
		ssup="th";
	}
	var mon=mDate.substr(3,2);
	var year=mDate.substr(6,4);
	var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
	var d = new Date(mon+"/"+date+"/"+year);
	var dayName = days[d.getDay()];
	var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August','September','October','November','December'];
	var monName = months[d.getMonth()];
	var hr=document.getElementById('hr').value;
	var mn=document.getElementById('mn').value;
	var ampm=document.getElementById('ampm').value;
	var venue=document.getElementById("venue").value;
	var socName= document.getElementById("sName").value;
	var mLastDate=document.getElementById("mlastdate").value;
	var tempId=document.getElementById("temp_type").value;
	$.ajax
	({
		url : "ajax/createMeeting.ajax.php",
		type : "POST",
		datatype: "JSON",
		data : {"method":"getTemplate","id":tempId},
			success : function(data)
			{	
				//alert ("Data:"+data);
				var meetingRes=Array();
				allData=data.split('#');
				meetingRes=JSON.parse(allData[0]);
						//alert("MeetingRes:"+meetingRes['Title']);
				var i;
				var id=0;
						//document.getElementById("agenda").style.display="table";
				document.getElementById("hide").style.display="table-row";
				document.getElementById("hide1").style.display="table-row";
				document.getElementById("hide2").style.display="table-row";
				document.getElementById("hide3").style.display="table-row";
				document.getElementById("hide4").style.display="table-row";
				document.getElementById("next").style.display="none";
				document.getElementById("notes").value=meetingRes['Notes'];
				document.getElementById("endText").value=meetingRes['EndText'];
				var meetingAgenda=JSON.parse(allData[1]);
				document.getElementById("srNo0").value=1;
				document.getElementById("agenda0").value=meetingAgenda[0];
				var len=meetingAgenda.length;
				//alert ("len:"+len)
				var sContent;
				for(i=1;i<len;i++)
				{
					sContent="<tr style='padding-top:1%;padding-bottom:10%' id='agendaRow"+i+"'><td width='30%' style='text-align:left;padding-left:15%'><b><font style='color:#F00'>*</font>Agenda:</b></td>";
                    sContent+="<td width='5%'><input type='text' id='srNo"+i+"' name='srNo"+i+"' placeholder='Sr. No.' style='width:40px;vertical-align:top' value='"+(i+1)+"'/><br><br><br><br><a id='del' onClick='deleteRow("+i+")' style='width:70%;height:120%;'><img src='images/del.gif' alt='Delete' style='width:70%;height:120%;padding-left:30%'></img></a><input type='hidden' id='aId"+i+"' name='aId"+i+"'/></td>";                   	
					sContent+="<td width='65%'><textarea id='agenda"+i+"' name='agenda"+i+"' cols='90' rows='6' placeholder='Enter the meeting agenda here' style='margin-left:1%'>"+meetingAgenda[i]+"</textarea></td></tr>";
					$("#agenda > tbody").append(sContent);
					scontent=" ";
				}
				for(i=0;i<len;i++)
				{
					if(i!=id)
					{
						document.getElementById("srNo"+i).value = i+1;
						document.getElementById("srNo"+i).readOnly = true;
					}
					else
					{
						continue;
					}
				}	
			}
		});
	//alert ("id:"+tempId);
	/*var notes="Dear Members, Notice is hereby given to all members that the "+title+" of "+socName+" will be held on "+dayName+", the "+date+" "+ssup+" of "+monName+", "+year+" at "+hr+":"+mn+" "+ampm+", "+venue+" to conduct the following businesses:";
	var eText="a. If there is no quorum till "+hr+":"+mn+" "+ampm+", the meeting shall be adjourned for another 30 minutes. Following that, the meeting will be held and conducted irrespective of whether there is a quorum or not.";
	eText+="<br>b. Only members of the society are eligible to attend and participate in the meeting according to the bye-laws of the society. No proxy attendance or voting will be allowed.";
	document.getElementById("next").style.display="none";
	document.getElementById("hide").style.display="table-row";
	document.getElementById("hide1").style.display="table-row";
	//document.getElementById("hide2").style.display="table-row";
	document.getElementById("hide3").style.display="table-row";
	document.getElementById("hide4").style.display="table-row";
	//document.getElementById("agenda").style.display="table";
	document.getElementById("notes").value=notes;
	document.getElementById("agenda1").value="Confirm and approve minutes of the "+title+" held on "+mLastDate;
	document.getElementById("agenda3").value="Consider and approve appointment of Auditors for the year "+(parseInt(year)-1)+"-"+year;
	document.getElementById("endText").value=eText;*/
}
function refreshNotes()
{
	var tempId=document.getElementById("temp_type").value;
	var conf = confirm("If you refresh it all changes you have made to Note and End Note will be erased. Are you sure , you want refresh it ???");
	if(conf==1)
	{
		var title=document.getElementById("title").value;
		var mDate=document.getElementById("mdate").value;
		var date=mDate.substr(0,2);
		var ssup,d;
		d=date.substr(1,1);
		if(d=="2")
		{
			ssup="nd";
		}
		else if(d=="3")
		{
			ssup="rd";
		}
		else
		{
			ssup="th";
		}
		var mon=mDate.substr(3,2);
		var year=mDate.substr(6,4);
		var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
		var d = new Date(mon+"/"+date+"/"+year);
		var dayName = days[d.getDay()];
		var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August','September','October','November','December'];
		var monName = months[d.getMonth()];
		var hr=document.getElementById('hr').value;
		var mn=document.getElementById('mn').value;
		var ampm=document.getElementById('ampm').value;
		time = hr+":"+mn+ampm;
		var venue=document.getElementById("venue").value;
		var socName= document.getElementById("sName").value;
		var mLastDate=document.getElementById("mlastdate").value;
		var notes="Dear Members, Notice is hereby given to all members that the "+title+" of "+socName+" will be held on "+dayName+", the "+date+" "+ssup+" of "+monName+", "+year+" at "+hr+":"+mn+" "+ampm+", "+venue+" to conduct the following businesses:";
		var eText="a. If there is no quorum till "+hr+":"+mn+" "+ampm+", the meeting shall be adjourned for another 30 minutes. Following that, the meeting will be held and conducted irrespective of whether there is a quorum or not.<br>b. Only members of the society are eligible to attend and participate in the meeting according to the bye-laws of the society. No proxy attendance or voting will be allowed.";
		document.getElementById("notes").value=notes;
		document.getElementById("endText").value=eText;
		document.getElementById("agenda1").value="Confirm and approve minutes of the "+title+" held on "+mLastDate;
		if(tempId==1)
		{
			document.getElementById("agenda3").value="Consider and approve appointment of Auditors for the year "+(parseInt(year)-1)+"-"+year;
		}
		if (tempId==2)
		{
			document.getElementById("agenda3").value="Consider and approve appointment of Auditors for the year "+(parseInt(year)-1)+"-"+year;
		}
		
	}
	else
	{
	}
}
function PreviewDetails()
{
	var method=document.getElementById("create").value;
	document.getElementById("preview_table").style.display="table-row";
	document.getElementById("cbHeader").style.display="block";
	var sName=document.getElementById("sName").value;
	//alert ("sName:"+sName);
	if(method=="Create")
	{
		$.ajax
		({
			url : "ajax/createMeeting.ajax.php",
			type : "POST",
			datatype: "JSON",
			data : {"method":"Preview", "mId":0, "socName":sName},
			success : function(data)
			{	
				//alert("Data:"+data);
				CKEDITOR.instances['events_desc'].setData(data);
				document.getElementById("finalRow").style.display="inline";	
				document.getElementById("print").style.display="inline";
				document.getElementById("gotoList").style.display="inline";
				//window.location.href ="meeting.php?type=open";
			}
		});
	}
	else if(method=="Update")
	{
		var mId=document.getElementById("mId").value;
		var sName=document.getElementById("sName").value;
		//alert ("sName:"+sName);
		mId = mId.trim();
		document.getElementById("preview_table").style.display="table-row";
		$.ajax
		({
			url : "ajax/createMeeting.ajax.php",
			type : "POST",
			datatype: "JSON",
			data : {"method":"Preview","mId":mId,"socName":sName},
			success : function(data)
			{	
				//alert("Data:"+data);
				CKEDITOR.instances['events_desc'].setData(data);
				document.getElementById("finalRow").style.display="inline";
				document.getElementById("print").style.display="inline";
				//window.location.href ="meeting.php?type=open";
			}
		});
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
function goto_notice()
{
	var title=document.getElementById("title").value;
	var gId = document.getElementById("grpname").value;
	title=title.trim().replace(/ /g,'%20');
	var mDate=document.getElementById("mdate").value;
	var ckeditor_data = CKEDITOR.instances.events_desc.getSnapshot().replace(/\n|\r/g, "");;
	var checks = document.getElementsByClassName('checkBox');
	var memAttendance = [];
	checks.forEach(function(val, index, ar)
	{
		if(ar[index].checked) 
		{
			if(ar[index].id != "")
			{
				memAttendance.push(ar[index].id);
			}
			else
			{		
				memAttendance = [];
				checks.forEach(function(val, index, ar) 
				{
					if(ar[index].id != "")
					{
						memAttendance.push(ar[index].id);
					}
				});
			}
        }
    });
	var i=0;
	if(memAttendance.length == 0)
	{
		//this array is empty
		memAttendance = [];
		checks.forEach(function(val, index, ar) 
		{
			if(ar[index].id != "")
			{
				memAttendance.push(ar[index].id);
			}
		});
	}
	var notice_to=JSON.stringify(memAttendance);
	var mId=document.getElementById("mId").value;
	mId=mId.trim();
	var strUrl="addnotice.php?module=4&unitid="+notice_to+"&ckeditor="+ckeditor_data+"&title="+title+"&mDate="+mDate+"&mId="+mId+"&queryType="+gId;
	window.location.href = strUrl;
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
			url : "ajax/createMeeting.ajax.php",
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
function deleteRow(id)
{
	var i = 0;
	var j = 1;
	var maxLen = parseInt(document.getElementById("maxrows").value);
	var len=parseInt(document.getElementById("maxlength").value);
	var method=document.getElementById("create").value;
	if(method=="Create")
	{
		for(i=0;i<=len;i++)
		{
			if(i<id)
			{
				var checkId = document.getElementById("srNo"+i);
				if(checkId)
				{
					document.getElementById("srNo"+i).value = j;
					j = j + 1;
					document.getElementById("srNo"+i).id = "srNo"+i;
				}
			}
			else if(i>id)
			{
				var checkId = document.getElementById("srNo"+i);
				if(checkId)
				{
					document.getElementById("srNo"+i).value = j;
					j = j + 1;
					document.getElementById("srNo"+i).id = "srNo"+i;
				}
			}
			else
			{
				document.getElementById("srNo"+i).id = "srNod";
				continue;
				//i = i + 1;
			}
		}
		maxLen = maxLen - 1;
		document.getElementById("maxrows").value=maxLen;
	}
	if(method=="Update")
	{
		var aId=document.getElementById("aId"+id).value;
		$.ajax
		({
			url : "ajax/createMeeting.ajax.php",
			type : "POST",
			datatype: "JSON",
			data : {"method":"deleteAgenda","aId":aId},
			success : function(data)
			{	
				//alert("Data:"+data);
				for(i=0;i<=len;i++)
				{
					if(i<id)
					{
						var checkId = document.getElementById("srNo"+i);
						if(checkId)
						{
							document.getElementById("srNo"+i).value = j;
							j = j + 1;
							document.getElementById("srNo"+i).id = "srNo"+i;
						}
					}
					else if(i>id)
					{
						var checkId = document.getElementById("srNo"+i);
						if(checkId)
						{
							document.getElementById("srNo"+i).value = j;
							j = j + 1;
							document.getElementById("srNo"+i).id = "srNo"+i;
						}
					}
					else
					{
						document.getElementById("srNo"+i).id = "srNod";
						continue;
				//i = i + 1;
					}
				}
				maxLen = maxLen - 1;
				document.getElementById("maxrows").value=maxLen;
			}
		});
	}
	document.getElementById("agendaRow"+id).style.display="none";	
}
function changeMaxRow(tempId)
{
	var selectedValue = tempId.value;
	if(selectedValue==3)
	{
		document.getElementById("maxrows").value=5;	
		document.getElementById("maxlength").value=5;	
	}
	else if(selectedValue==4)
	{
		document.getElementById("maxrows").value=6;	
		document.getElementById("maxlength").value=6;	
	}
	else
	{
		document.getElementById("maxrows").value=7;	
		document.getElementById("maxlength").value=7;	
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
		/*document.getElementById('msgDiv').style.display = 'none';
   		var w =  $('#searchbox').val();
        if (w)
		 {
				if($('#mem_id li:Contains('+w+')').length == 0)
				{
					$('#mem_id li').hide();
					document.getElementById('msgDiv').style.display = 'block';
					document.getElementById('msgDiv').innerHTML = '<font style="color:#F00;"><b>No Match Found...</b></font> ';
				}
				else
				{
					$('#mem_id li').hide();
					$('#mem_id li:Contains('+w+')').show();	
				}
		} 
		else 
		{
			 $('#mem_id li').show();                  
        }*/
}
function SearchElement()
{
		//document.getElementById('msgDiv2').style.display = 'none';
   		var w =  $('#searchbox2').val();
        if (w)
		 {
				if($('#selectedMemId li:Contains('+w+')').length == 0)
				{
					$('#selectedMemId li').hide();
					document.getElementById('msgDiv').style.display = 'block';
					document.getElementById('msgDiv').innerHTML = '<font style="color:#F00;"><b>No Match Found...</b></font> ';
				}
				else
				{
					$('#selectedMemId li').hide();
					$('#selectedMemId li:Contains('+w+')').show();	
				}
		} 
		else 
		{
			 $('#selectedMemId li').show();                  
        }
}

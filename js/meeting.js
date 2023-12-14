function getmeeting(str)
{
	var iden	= new Array();
	iden		= str.split("-");
	
	<!--$('html, body').animate({ scrollTop: $('#top').offset().top }, 300);-->
	//alert ("String given:"+str);
	if(iden[0]=="delete")
	{
		var conf = confirm("Are you sure , you want to delete it ???");
		if(conf==1)
		{			
			$.ajax
			({
				url : "ajax/meeting.ajax.php",
				type : "POST",
				datatype: "JSON",
				data : {"method":"delete","mId":iden[1]},
				success : function(data)
				{	
					//alert ("Data:"+data);
					window.location.href ="meeting.php?type=open";
				}	
			});
		}
	}
	else
	{	
		//document.getElementById("hide").style.display="table";	
		//remoteCall("ajax/meeting.ajax.php","form=meeting&method="+iden[0]+"&mId="+iden[1],"loadchanges");
		window.location.href="createMeeting.php?method=edit&mId="+iden[1];
	}
}
function getminutes(str)
{
	var iden	= new Array();
	iden		= str.split("-");
	
	<!--$('html, body').animate({ scrollTop: $('#top').offset().top }, 300);-->
	//alert ("String given:"+str);
	if(iden[0]=="delete")
	{
		var conf = confirm("Are you sure , you want to delete it ???");
		if(conf==1)
		{			
			remoteCall("ajax/meeting.ajax.php","form=meeting&method="+iden[0]+"&mId="+iden[1],"loadchanges");
		}
	}
	else
	{	
		//document.getElementById("hide").style.display="table";	
		//remoteCall("ajax/meeting.ajax.php","form=meeting&method="+iden[0]+"&mId="+iden[1],"loadchanges");
		window.location.href="minutesOfMeeting.php?method=create&mId="+iden[1];
	}
}
function viewMeeting(str)
{
	var iden	= new Array();
	iden		= str.split("-");
	window.open("viewMeeting.php?mId="+iden[1]);
	
}
function viewMinutes(str)
{
	//alert ("in viewMinutes");
	var iden	= new Array();
	iden		= str.split("-");
	window.open("viewMinutes.php?mId="+iden[1]);
}
function loadchanges()
{
	//alert (sResponse);
  	var a    = sResponse.trim();
  	///alert ("loadchanges:"+a); 
	var arr1	= new Array();
	var arr2	= new Array();
	var arr3	= new Array();
	var time    = new Array();		
	arr1		= a.split("@@@");
	arr2		= arr1[1].split("#");
	//alert ("arr1:"+arr1[0]); 
	if(arr1[0] == "edit")
	{		
		time = arr2[2].split(":");
		var str= time[1];
		//alert ("String:  "+str);
		//alert ("time of 1  :"+time[1]);
		var mn =str.substr(0,2);
		var ampm= str.substr(2,2);
		//alert ("String:  "+mn);
		document.getElementById('mdate').value=arr2[1];
		document.getElementById('hr').selectedIndex=parseInt(time[0]);
		document.getElementById('mn').value=mn;
		document.getElementById('ampm').value=ampm;
		document.getElementById('venue').value=arr2[3];
		document.getElementById("id").value=arr2[0];
		document.getElementById("insert").value="Update";
	}
	else if(arr1[0] == "delete")
	{		
		//alert( "hiii");
		window.location.href ="meeting.php";
		//location.reload(true);
	}
}
function cancelMeeting(mId)
{
	alert ("cancelMeeting :"+mId);
	$.ajax
	({
		url : "ajax/meeting.ajax.php",
		type : "POST",
		datatype: "JSON",
		data : {"method":"cancelMeeting","mId":mId,"status":"5"},
		success : function(data)
		{	
			//alert ("Data:"+data);
			window.location.href ="meeting.php?type=cancel";
		}	
	});
}
function goTOViewPage(meetingId)
{
	//alert ("meetingId = "+meetingId);
	window.open("viewMeeting.php?mId="+meetingId+"&type=open");
}
function val()
{
	var schedule_name = trim(document.getElementById('schedulename').value);	    
	var round_type = trim(document.getElementById('roundtype').value);
    var frequency = trim(document.getElementById('frequency').value);
	var time = trim(document.getElementById('time').value);

	if(schedule_name=="")
	{	
		document.getElementById('error').style.display = '';	
		document.getElementById('error').innerHTML = "Select the schedule name";
		document.getElementById('schedulename').focus();
		go_error();
		return false;
	}

   if(round_type=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Select the round type";
		document.getElementById("roundtype").focus();		
		go_error();
		return false;
	}
    if(frequency=="0")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Select the frequency";
		document.getElementById("frequency").focus();		
		go_error();
		return false;
	}
    if(time=="Please Select")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Select the time";
		document.getElementById("time").focus();		
		go_error();
		return false;
	}
    function LTrim( value )
	{
	var re = /\s*((\S+\s*)*)/;
	return value.replace(re, "$1");
	}
	function RTrim( value )
	{
	var re = /((\s*\S+)*)\s*/;
	return value.replace(re, "$1");
	}
	function trim( value )
	{
	return LTrim(RTrim(value));
	}
}

function get_checkpost_master_edt(Id)
{
//alert(Id);
$.ajax({
url : "ajax/ajaxschedule_list.php",
type : "post",
data : {"method":"edit","id":Id},
success : function(data)
{
    console.log(data);
    var arr1 = new Array();
    var arr2 = new Array();
    var arr3 = new Array();
    arr1 = data.split("@@@");
    console.log(arr1);
    arr2 = JSON.parse("["+arr1[1]+"]");
    arr3=arr2[0];
    document.getElementById('schedulename').value=arr3[0].schedule_name;
    document.getElementById('roundtype').value=arr3[0].round_id;
    document.getElementById('frequency').value=arr3[0].frequency;
    document.getElementById('time').value=arr3[0].round_time;

    document.getElementById('insert').value="Update";
}
})
}

function get_checkpost_master_del(id)
{    
    if(id>0)
    {        
        var conf = confirm("Are you sure , you want to delete it ?");
        if(conf==1)
        {
            $.ajax({
                url : "ajax/ajaxschedule_list.php",
                type : "POST",
                data: {"method":"delete","id":id} ,
                success : function(data)
                {                   
                    //console.log("testdelete",data);                    
                     window.location.href='shedule_list.php';     
                }
            });        
        }
        else
        {
           window.location.href='shedule_list.php';
        }
    }
}
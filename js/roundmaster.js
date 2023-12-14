function val()
{
	var name=trim(document.getElementById('master_names').value);
	var chkcount=trim(document.getElementById('count_checkpost_id').value);
	var kkk1 =false;
	
	if(name=="")
	{	
		document.getElementById('error').innerHTML = "Please enter master name ";
		alert(document.getElementById('error').innerHTML);
		document.getElementById('master_names').focus();
		go_error();
		return false;
	}
	
	for(var i1=0; i1<= chkcount-1;i1++)
	{
		
		if(document.getElementById('checkpost_id'+i1).checked==true)
		{
			 kkk1 = true;
		}
		
	}
	
	if(kkk1== false)
	{
		document.getElementById('error').style.display = '';
		document.getElementById('error').innerHTML = "Please Select Atleast One checkpost";
		alert(document.getElementById('error').innerHTML);
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
url : "ajax/ajaxround_master.php",
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
    document.getElementById('master_names').value=arr3[0].Name;
	document.getElementById('desc').value=arr3[0].Description;
	console.log(arr3[0].Checkpost);
	//var chkLenght =arr3[0].Checkpost.length;
	var chkLenght =document.getElementById('count_checkpost_id').value;
	//alert(chkLenght);
	for(var i =0 ; i< chkLenght ; i++)
	{
		var ck =document.getElementById('checkpost_id'+i).value;
		var comparevalue = arr3[0].Checkpost.includes(ck);
		if(comparevalue == true)
		{
			document.getElementById('checkpost_id'+i).checked=true;
		}
		
	}
   
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
                url : "ajax/ajaxround_master.php",
                type : "POST",
                data: {"method":"delete","id":id} ,
                success : function(data)
                {                   
                    //console.log("testdelete",data);                    
                     window.location.href='round_master.php';     
                }
            });        
        }
        else
        {
           window.location.href='round_master.php';
        }
    }
}
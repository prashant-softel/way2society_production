function val()
{
	
	var name=trim(document.getElementById('name1').value);
	
	
	if(name=="")
	{	
		document.getElementById('error').innerHTML = "Please enter checkpost name ";
		alert(document.getElementById('error').innerHTML);
		document.getElementById('name1').focus();
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
url : "ajax/ajaxcheckpost_master.php",
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
    document.getElementById('name1').value=arr3[0].checkpost_name;
    document.getElementById('desc').value=arr3[0].desc;
    //document.getElementById('id1').value=arr3[0].qrcode;
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
                url : "ajax/ajaxcheckpost_master.php",
                type : "POST",
                data: {"method":"delete","id":id} ,
                success : function(data)
                {                   
                    //console.log("testdelete",data);                    
                     window.location.href='checkpost_master.php';     
                }
            });        
        }
        else
        {
           window.location.href='checkpost_master.php';
        }
    }
}
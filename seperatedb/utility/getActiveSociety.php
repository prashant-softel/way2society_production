<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Way2Society - Update Tables</title>
<script type="text/javascript" language="javascript" src="js/jquery.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="/resources/demos/style.css">
  <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>


<script>
	function ViewDetails()
	{
		var ClientId = document.getElementById('client_id').value.trim();
		//var PostDate = document.getElementById('post_date').value.trim();
		//alert(ClientId);
		//alert(PostDate);
		//var query = document.getElementById('query').value.trim();
		
		//alert(startNo + ":" + endNo + ":" + query);
		
			//document.getElementById('msg').innerHTML = 'Message Log : <br />Getting Details. Please Wait...';
			$.ajax({
				url : "script/getsociety.script.php",
				type : "POST",
				data: { "clientId": ClientId, 
						},
				success : function(data)
				{
					console.log(data);
					var result =JSON.parse(data);
					console.log(result.length);
					var tbl = '<table border="2px" style="width:100%;text-align:center;">';
					  	tbl += '<tr><th>Sr No.</th>';
					  	tbl += '<th>Society Name</th>';
						tbl += '<th>Accounts Name</th>';
						tbl += '<th>Member Count</th>';
						
						tbl += '<th>For Period</th>';
					 	tbl += '<th>Bill Date</th>';
						//tbl += '<th>Last Bill Register TimeStamp</th></tr>';
						tbl +='</tr>';
						var cnt =1;
						for(var i=0; i<result.length; i++)
						{
							tbl +='<tr>';
							tbl +='<td>'+cnt+'</td>';
							tbl +='<td>'+result[i]['society_name']+'</td>';
							tbl +='<td>'+result[i]['clientName']+'</td>';
							tbl +='<td>'+result[i]['Total_mamber']+'</td>';
							tbl +='<td>'+result[i]['ForPeriod']+'</td>';
							tbl +='<td>'+result[i]['BillDate']+'</td>';
							//tbl +='<td>'+result[i]['ForPeriod']+'</td>';
							tbl +='</tr>';
							cnt++;
						}
						tbl +='</table>';
						$("#societyTble").html(tbl);
						document.getElementById('total').innerHTML=cnt;
						//alert(data);
						//document.getElementById('msg').innerHTML += data;
				},
				fail: function()
				{
					
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) 
				{
					
				}
			});
		
		/*else
		{
			document.getElementById('msg').innerHTML = 'Message Log : <br />Please enter all fields.';
		}*/
	}
	
	
</script>
<script type="text/javascript">
        $(function()
        {
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker({ 
            dateFormat: "dd-mm-yy", 
            showOn: "both", 
           //buttonImage: "images/calendar.gif", 
            //buttonImageOnly: true 
        })});
		
		function ExportToExcel()
		{
		 var myBlob =  new Blob( [$("#body").html()] , {type:'application/vnd.ms-excel'});
		 var url = window.URL.createObjectURL(myBlob);
		 
		 var a = document.createElement("a");
		 document.body.appendChild(a);
		 a.href = url;
		 a.download = "SocietyList.xls";
		 a.click();
		//adding some delay in removing the dynamically created link solved the problem in FireFox
		 setTimeout(function() {window.URL.revokeObjectURL(url);},0);
	}
  </script>
</head>

<?php
include('script/config_script.php');

try
{
$hostname = DB_HOST;
$username =DB_USER;
$password = DB_PASSWORD;
$dbname = 'hostmjbt_societydb';
$mMysqli = mysqli_connect($hostname, $username, $password, $dbname);

$query ="select distinct id,client_name as ClientName from client order by client_name asc";
$result = mysqli_query($mMysqli, $query);
$count = 0;
while($row = $result->fetch_array(MYSQL_ASSOC))
{	
	$data[$count] = $row;
	$count++;
}
mysqli_close($mMysqli);
//var_dump($data);
}
catch(Exception $exp)
{
	echo $exp;
}
?>
<body>
<table style="width: 100%;" >
<tr><td colspan="3"></td></tr>
<tr>
<td style="width: 25%;"></td>
<td style="width: 50%;text-align: center;">
<b>Client Name : </b><select name="client_id" id="client_id" style="width:142px;" >
						<?php 
						foreach($data as $value)
						{?>
							<option  value="<?php echo $value['id']?>"><?php echo $value['ClientName'] ?></option>
						<?php } ?>

					</select>
	<br /><br />
	<!--<b>Filter Date &nbsp;&nbsp;: </b><input type="text" name="post_date" id="post_date" value="<?php //echo date('d-m-Y');?>"  class="basics" size="10" readonly  style="width:80px;"/>-->
	
	<br /><br />
	<input type="button" value="Submit" onclick="ViewDetails();" />
    <input type="button" value="Export to excel" onclick="ExportToExcel();" />
	<br /><br />
    </div>
</td><td style="width: 25%;"></td></tr></table>
	<div id="body">
    <table>
    <tr>
    <th>Total Society : <span id="total"></span></th>
    
    </tr>
    </table>
    <div id ="societyTble">
   
    </div>
    </div>
    
	
</body>
</html>
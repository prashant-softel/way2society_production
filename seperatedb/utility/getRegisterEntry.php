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
		var fromdate = document.getElementById('from_date').value.trim();
		var todate = document.getElementById('to_date').value.trim();
		var startDB = document.getElementById('start').value.trim();
		var endDB = document.getElementById('end').value.trim();
		//var PostDate = document.getElementById('post_date').value.trim();
		
		//var query = document.getElementById('query').value.trim();
		
		//alert(startNo + ":" + endNo + ":" + query);
		
			//document.getElementById('msg').innerHTML = 'Message Log : <br />Getting Details. Please Wait...';
			$.ajax({
				url : "script/getregister.script.php",
				type : "POST",
				data: { "fromDate": fromdate, 
				 		"toDate": todate, 
						"startDB": startDB, 
						"endDB": endDB, 
						},
				success : function(data)
				{
					
					var result =JSON.parse(data);
					//console.log(result.);
					//console.log(Object.keys(result).length);
					var tbl = '';
					Object.keys(result).forEach(key => {
 					 console.log(key, result[key]);
					 var result1= result[key];
					  tbl += '<h3 style="text-align:center">'+key+'</h3>';
					 	tbl += '<table border="2px" style="width:100%;text-align:center;">';
					  	tbl += '<tr><th>Sr No.</th>';
					  	//tbl += '<th>Society Name</th>';
						tbl += '<th>MONTH</th>';
						tbl += '<th>CHEQUE</th>';
						
						tbl += '<th>NEFT</th>';
					 	tbl += '<th>PAYMENT GATEWAY</th>';
						tbl += '<th>CASH</th>';
						//tbl += '<th>Last Bill Register TimeStamp</th></tr>';
						tbl +='</tr>';
						cnt =1;
						Object.keys(result1).forEach(key => {
							//console.log(key,result1[key]);
							let Cheque=0;
							let Neft=0;
							let Paytm=0;
							let Cash=0;
							if(result1[key]['CHEQUE'] ==undefined || result1[key]['CHEQUE'] =="")
							{
								Cheque=0;
							}
							else
							{
								Cheque=result1[key]['CHEQUE'];
							}
							if(result1[key]['NEFT'] ==undefined || result1[key]['NEFT'] =="")
							{
								Neft=0;
							}
							else
							{
								Neft=result1[key]['NEFT'];
							}
							if(result1[key]['PayTM'] ==undefined || result1[key]['PayTM'] =="")
							{
								Paytm=0;
							}
							else
							{
								Paytm=result1[key]['PayTM'];
							}
							if(result1[key]['CASH'] ==undefined || result1[key]['CASH'] =="")
							{
								Cash=0;
							}
							else
							{
								Cash=result1[key]['CASH'];
							}
							tbl += '<tr><td>'+cnt +'</td>';
							tbl += '<td>'+key+'</td>';
							tbl += '<td>'+Cheque+'</td>';
							tbl += '<td>'+Neft+'</td>';
							tbl += '<td>'+Paytm+'</td>';
							tbl += '<td>'+Cash+'</td></tr>';
							
							cnt++;
							//console.log(key,result1[key]['CHEQUE']);
							});
							console.log(Object.keys(result[key]).length);
						//var cnt =1;
					 tbl +='</table><hr>';
					});
				
						/*for(var i=0; i<result.length; i++)
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
						}*/
						
						$("#societyTble").html(tbl);
						//document.getElementById('total').innerHTML=cnt;
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


<body>
<table style="width: 100%;" >
<tr><td colspan="3"></td></tr>
<tr>
<td style="width: 25%;"></td>
<td style="width: 50%;text-align: center;">

<b>From Date &nbsp;&nbsp;: </b><input type="text" name="from_date" id="from_date" value="<?php //echo date('d-m-Y');?>"  class="basics" size="10" readonly  style="width:80px;"/>
<b>To Date &nbsp;&nbsp;: </b><input type="text" name="to_date" id="to_date" value="<?php //echo date('d-m-Y');?>"  class="basics" size="10" readonly  style="width:80px;"/>
	
	<br /><br />
    <b>Start DB Name : </b>hostmjbt_society&nbsp;<input type="text" name="start" id="start" style="width: 80px;" />
    <b>End DB Name &nbsp;&nbsp;: </b>hostmjbt_society&nbsp;<input type="text" name="end" id="end"  style="width: 80px;" />
    <br /><br />
	<input type="button" value="Submit" onclick="ViewDetails();" />
    <input type="button" value="Export to excel" onclick="ExportToExcel();" />
	<br /><br />
    </div>
</td><td style="width: 25%;"></td></tr></table>
<hr>
	<div id="body">
    <table>
    <tr>
   <!-- <th>Total Society : <span id="total"></span></th>-->
    
    </tr>
    </table>
    <div id ="societyTble">
   
    </div>
    </div>
    
	
</body>
</html>
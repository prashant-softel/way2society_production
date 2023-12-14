// JavaScript Document
function get_period(year_id)
{
	
	document.getElementById('error_period').style.display = '';	
	document.getElementById('error_period').innerHTML = 'Fetching Period. Please Wait...';	
	//Hide the above msg after 3 sec we call go_error() method
	go_error();	
	if(year_id == null)
	{
		populateDDListAndTrigger('select#period_id', 'ajax/ajaxbill_period.php?getperiod&year=' + document.getElementById('year_id').value, 'period', 'periodFetched', false);
	}
	else
	{
		populateDDListAndTrigger('select#period_id', 'ajax/ajaxbill_period.php?getperiod&year=' + year_id, 'period', 'periodFetched', false);
	}
}

function periodFetched()
{
	document.getElementById('error_period').style.display = '&nbsp;';	
}
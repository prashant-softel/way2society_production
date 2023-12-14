
function ExpandALL()
{
	
	$('.tree li:has(ul)').addClass('parent_li').find(' > label ').attr('title', 'Collapse this branch');
    var children = $('.tree li.parent_li > label ').parent('li.parent_li').find(' > ul > li');
	if (children.is(":visible")) {
		//children.hide('fast');
		//$(this).attr('title', 'Expand this branch').find(' > span').addClass('icon-plus-sign').removeClass('icon-minus-sign');
	} else {
		
		children.show('fast');
		
		$(this).attr('title', 'Collapse this branch').find(' > div').addClass('icon-minus-sign').removeClass('icon-plus-sign');
	}
        
   
}



function CollapseALL()
{
	$('.tree li:has(ul)').addClass('parent_li').find(' > label ').attr('title', 'Expand this branch');
    var children = $('.tree li.parent_li > label ').parent('li.parent_li').find(' > ul > li');
	if (children.is(":visible")) {
		children.hide('fast');
		$(this).attr('title', 'Expand this branch').find(' > div').addClass('icon-plus-sign').removeClass('icon-minus-sign');
	} else {
		//children.show('fast');
		
		//$(this).attr('title', 'Collapse this branch').find(' > span').addClass('icon-minus-sign').removeClass('icon-plus-sign');
	}
        
}

function reportTypeChange()
{
		var repprtType = $('#report_type').val();
		var url = "TrialBalance.php?q=" + repprtType;
		if (document.getElementById('checkbox1').checked) {
            url = url + '&show=1';
        } else {
            url = url + '&show=0';
        }
	   //window.location.href ="TrialBalance.php?q=" + repprtType;
	   window.location.href = url;
}



function ExportToExcel()
{
	var originalDiv = document.getElementById("Exportdiv").innerHTML;	
	document.getElementById('Exportdiv').style.display = 'block';
	
	//Strip out the hidden columns <td>,<th> that are hidden within your table
    $("#Exportdiv").find('[style*="display: none"]').remove();
	$("td").find('a').contents().unwrap();
	
	var myBlob =  new Blob( ['<html><head><style>.ExportStyle{border-collapse: collapse;}.rowstyle > td,th{ border-collapse: collapse;border:1px solid black;  }</style></head><body>' + $("#Exportdiv").html() + '</body></html>'] , {type:'application/vnd.ms-excel'});
	var url = window.URL.createObjectURL(myBlob);
	
	var a = document.createElement("a");
	document.body.appendChild(a);
	a.href = url;
	a.download = "download.xls";
	a.click();
	document.getElementById('Exportdiv').style.display = 'none';
    document.getElementById("Exportdiv").innerHTML = originalDiv;
   //adding some delay in removing the dynamically created link solved the problem in FireFox
	setTimeout(function() {window.URL.revokeObjectURL(url);},0);
	e.preventDefault();	
	
}

function togglePrevAmt(id)
{
	if (document.getElementById(id).checked == true) 
	{
		$("td.Previous").show();
		$("th.Previous").show();
		$("th.Previous").css('width','25%');
		$("td.Previous").css('width','25%');
		$("td.Current").css('width','25%');
		$("th.Current").css('width','25%');
		$("td.colchange").attr('colspan','5');
	}
	else
	{
		$("td.Previous").hide();
		$("th.Previous").hide();
		$("td.Previous").css('width','0%');
		$("td.Current").css('width','50%');
		$("th.Current").css('width','50%');
		$("td.colchange").attr('colspan','3');
		
	}
	
	$("th.icon-class").css('width','10px');
	$("td.icon-class").css('width','10px');
	
}
function SubmitForm()
{

	let startDate = $('#start_date').val();
	let endDate = $('#end_date').val();
	let module = $("#module_name").val();
	let userName = $("#user_name").val();

	if(startDate == ""  || endDate == ""){

		$(".error_msg").html('Please fill the required fields!!');
		return false;
	}


	$.ajax({
	type: "POST",
	url: "ajax/ajaxChangeLog.php",
	data: {"startDate" : startDate,"endDate" : endDate , "module" : module, "login_id" : userName,"method" : 'fetchLog'},
	
	success: function(result)
		{
			document.getElementById('FilterData').innerHTML=result;
			$('#example').dataTable().fnDestroy();
			$('#example').DataTable( {
				dom: 'T<"clear">lfrtip',
				"aLengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
				buttons: [
					'copy', 'excel', 'pdf'
				]
			} );
			
			document.getElementById('example').style.width='100%';
		}
	});

	
	
}
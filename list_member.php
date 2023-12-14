
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
<title>W2S - List of members</title>
</head>


<?php include_once("includes/head_s.php");
?>
<?php
include_once("classes/list_member.class.php");

$obj_list_member = new list_member($m_dbConn);
?>
<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<link href="css/messagebox.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/ajax.js"></script>      
    <script type="text/javascript" src="js/jsViewLedgerDetails.js"></script>      
	<script type="text/javascript" src="js/ajax_new.js"></script> 
	<script language="javascript" type="application/javascript">
function go_error()
{
	setTimeout('hide_error()',10000);	
}
function hide_error()
{
	document.getElementById('error_del').style.display = 'none';	
}
function del_member(member_id)
{
	var conf = confirm("Are you sure you want to delete it ?");
	if(conf==true)
	{
		document.getElementById('error_del').style.display = '';	
		document.getElementById('error_del').innerHTML = 'Wait...';		
		remoteCall("ajax/del_member.php","member_id="+member_id,"res_del_member");
	}
}
function res_del_member()
{
	var res = sResponse;	//alert(res);
	window.location.href = 'list_member.php?scm&tm=<?php echo time();?>&del';
}

function get_wing(society_id)
{
	document.getElementById('error_del').style.display = '';	
	document.getElementById('error_del').innerHTML = 'Wait... Fetching wing under this society';	
	remoteCall("ajax/get_wing.php","society_id="+society_id,"res_get_wing");		
}

function res_get_wing()
{
	var res = sResponse;
	
	document.getElementById('error_del').style.display = 'none';	
	
	var count = res.split('****');
	var pp = count[0].split('###');
	
	document.getElementById('wing_id').options.length = 0;
	var that = document.getElementById('society_id').value;
	/*
	if(count[1]!=0)
	{
		if(that>0) 
		{*/
			for(var i=0;i<count[1];i++) 
			{		
				var kk = pp[i].split('#');
				var wing_id = kk[0];
				var wing = kk[1];
				document.getElementById('wing_id').options[i] = new Option(wing,wing_id);
			}
			document.getElementById('wing_id').options[i] = new Option('All','');
			document.getElementById('wing_id').value = '';
		/*}
	}
	else
	{
		document.getElementById('wing_id').options[0] = new Option('Please Select','');
	}*/
}

$(document).ready(function() {
	 $('#example').dataTable(
 {
	"bDestroy": true
}).fnDestroy();
if(localStorage.getItem("client_id") != "" && localStorage.getItem("client_id") != 1)
{
	//alert("hey");
		$('#example').dataTable( 
					
					{
						//alert("Hello");
						"stateSave": true,
						"stateDuration": 0,
						dom: 'T<"clear">Blfrtip',
						"aLengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All data"] ],
						buttons: 
						[
							{
								extend: 'colvis',
								width:'inherit'/*,
								collectionLayout: 'fixed three-column'*/
							}
							
						],
					 columnDefs: 
					 [
						{
							targets:[9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25],
							visible: false
						}
					],
						"oTableTools": 
						{
							"aButtons": 
							[
								{ "sExtends": "copy", "mColumns": "visible" },
								{ "sExtends": "csv", "mColumns": "visible" },
								{ "sExtends": "xls", "mColumns": "visible" },
								{ "sExtends": "pdf", "mColumns": "visible"},
								{ "sExtends": "print", "mColumns": "visible","sMessage": printMessage + " "}
							],
						 "sRowSelect": "multi"
					},
					aaSorting : [],
						
					fnInitComplete: function ( oSettings, json ) {
						//var otb = $(".DTTT_container")
						//alert("fnInitComplete");
						$(".DTTT_container").append($(".dt-button"));
						
						//get sum of amount in column at footer by class name sum
						this.api().columns('.sum').every(function(){
						var column = this;
						var total = 0;
						var sum = column
							.data()
							.reduce(function (a, b) {
								if(a.length == 0)
								{
									a = '0.00';
								} 
								if(b.length == 0)
								{
									b = '0.00';
								}
								var val1 = parseFloat( String(a).replace(/,/g,'') ).toFixed(2);
								var val2 = parseFloat(String(b).replace(/,/g,'') ).toFixed(2);
								total = parseFloat(parseFloat(val1)+parseFloat(val2));
								return  total;
							});
					$(column.footer()).html(format(sum,2));
					});
					
					}
					
				} );	
				//alert("End If");
	}
	else
	{
			$('#example').dataTable( {
						/*dom: 'T<"clear">lfrtip',*/
						"stateSave": true,
						"stateDuration": 0,
						dom: 'T<"clear">Blfrtip',
						"aLengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All data"] ],
						buttons: 
						[
							{
								extend: 'colvis',
								width:'inherit'/*,
								collectionLayout: 'fixed three-column'*/
							}
						],
					 columnDefs: 
					 [
						{
							targets: 9,
							visible: false
						}
					],
						"oTableTools": 
						{
							"aButtons": 
							[
								{ "sExtends": "copy", "mColumns": "visible" },
								{ "sExtends": "csv", "mColumns": "visible" },
								{ "sExtends": "xls", "mColumns": "visible" },
								{ "sExtends": "pdf", "mColumns": "visible"},
								{ "sExtends": "print", "mColumns": "visible","sMessage": printMessage + " "}
							],
						 "sRowSelect": "multi"
					},
					aaSorting : [],
						
					fnInitComplete: function ( oSettings, json ) {
						//var otb = $(".DTTT_container")
						//alert("fnInitComplete");
						$(".DTTT_container").append($(".dt-button"));
						
						//get sum of amount in column at footer by class name sum
						this.api().columns('.sum').every(function(){
						var column = this;
						var total = 0;
						var sum = column
							.data()
							.reduce(function (a, b) {
								if(a.length == 0)
								{
									a = '0.00';
								} 
								if(b.length == 0)
								{
									b = '0.00';
								}
								var val1 = parseFloat( String(a).replace(/,/g,'') ).toFixed(2);
								var val2 = parseFloat(String(b).replace(/,/g,'') ).toFixed(2);
								total = parseFloat(parseFloat(val1)+parseFloat(val2));
								return  total;
							});
					$(column.footer()).html(format(sum,2));
					});
					
					}
					
				} );	
			//alert("End");
	}
//alert("End of function");
});		

</script>

<?php if(isset($_REQUEST['del'])){ ?>
<body onLoad="go_error();">
<?php }else{ ?>
<body>
<?php } ?>

<!--<center><h2><font color="#43729F"><b><?php //echo $obj_list_member->display_society_name($_SESSION['society_id']);?></b></font></h2>-->
<br><center>
<div class="panel panel-info" id="panel" style="display:none">
<div class="panel-heading" id="pageheader">List of Members</div>
<?php if(isset($_SESSION['role']) && $_SESSION['role']=='Super Admin'){?>

<!--<a href="member_main_new.php?scm" style="color:#00F; text-decoration:none;"><b>Add New Member</b></a>
</center>-->

<?php } ?>

<center>
	<br>
<!--<a href="unit.php?imp&ssid=<?php echo $_SESSION['society_id'];?>&idd=<?php echo time();?>"><input type="button" value="Add Unit"></a>-->
<?php if($_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['profile'][PROFILE_MANAGE_MASTER] == 1) { ?>
<button type="button" class="btn btn-primary" onClick="window.location.href='unit.php?imp&ssid=<?php echo $_SESSION['society_id'];?>&idd=<?php echo time();?>'" style="float:center;margin-right:5%">Add New Unit</button>
<?php } ?>
<table align="center" border="0" style="width:100%">
<tr>
	<td valign="top" align="center"><font color="red"><?php if(isset($_GET['del'])){echo "<b id=error_del>Record deleted Successfully</b>";}else{echo '<b id=error_del></b>';} ?></font></td>
</tr>
<tr>
<td>
<?php
echo "<br>";
$str1 = $obj_list_member->pgnation();
//echo "<br>";
//echo $str = $obj_list_member->display1($str1);
//echo "<br>";
//$str1 = $obj_list_member->pgnation();
//echo "<br>";
?>
</td>
</tr>
</table>




</center>
</div>
</center>
<?php include_once "includes/foot.php"; ?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>

<title>W2S - User Management</title>
</head>


<?php //include_once "ses_set_as.php"; ?>
<?php 
include_once("includes/head_s.php");
//include_once("datatools.php");

include_once("classes/add_member_id.class.php");
$obj_add_member_id = new add_member_id($m_dbConn, $m_dbConnRoot);
?>
 

<html>
<head>
	<!--<link rel="stylesheet" type="text/css" href="css/pagination.css" >-->
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/jsadd_member_id_20201116.js"></script>
    <script type="text/javascript" src="js/validate.js"></script>
    <script type="text/javascript" src="js/ajax_new.js"></script> 
    <link href="css/messagebox.css" rel="stylesheet" type="text/css" />
    <style type="text/css" class="init">
		.loader 
		{
			position: fixed;
			left: 0px;
			top: 0px;
			width: 100%;
			height: 100%;
			z-index: 9999;
			opacity:0.8;
			background: url('images/loader/page-loader.gif') 50% 50% no-repeat rgb(114,118,122);
		}
		.modal-body {
		  overflow-x: auto;
		}
		.table-responsive {
			max-height:300px;
		}
	</style>
 <script language="javascript" type="application/javascript">
 document.write('<div class="loader"></div>');
  hideLoaderFast();
	

	function go_error()
    {
        setTimeout('hide_error()',6000);	
    }
    function hide_error()
    {
        document.getElementById('error').style.display = 'none';	
    }
	</script>
    
    <script language="javascript" type="application/javascript">
	function get_mem_info(com_id)
	{
		remoteCall("ajax/get_mem_info.php","com_id="+com_id,"res_get_mem_info");
	}
	
	function res_get_mem_info()
	{
		var res = sResponse;	
		var res1 = res.split('#');
		
		if(res1[0]==1)
		{
			document.getElementById('member_id').value = res1[2];
			document.getElementById('password').value  = res1[3];
			document.getElementById("id").value = res1[1];
			
			document.getElementById("insert").value = "Update";
		}
		else
		{
			document.getElementById('member_id').value = '';
			document.getElementById('password').value  = '';
			document.getElementById("id").value = '';
			
			document.getElementById("insert").value = "Insert";	
		}
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
				
		for(var i=0;i<count[1];i++) 
		{		
			var kk = pp[i].split('#');
			var wing_id = kk[0];
			var wing = kk[1];
			document.getElementById('wing_id').options[i] = new Option(wing,wing_id);
		}
		document.getElementById('wing_id').options[i] = new Option('All','');
		document.getElementById('wing_id').value = '';
	}

	</script>
</head>

<?php if(isset($_POST['ShowData']) || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])){ ?>
<body onLoad="go_error();">
<?php }else{ ?>
<body>
<?php } ?>

<br>
<center>
<div class="panel panel-info" id="panel" style="display:none">
<div class="panel-heading" id="pageheader">User Management</div>
<!--<center><font color="#43729F" size="+1"><b>Member Login Code</b></font></center>-->
<br>
<a href="adduser.php"><input type="button" value="Add New User" style="color: #fff;background-color: #337ab7;border-color: #2e6da4;width:13%;height:20%;"></a>

<?php if($sadmin==1){?>
<br>
<form method="post">
<table align="center" border="0">
<tr>
	<td valign="top" align="center"><font color="red"><b id="error_del"></b></font></td>
</tr>
</table>

<!--<table align="center" border="0">
<?php if(isset($_SESSION['sadmin'])){?>
<tr align="left">
	<td>Society</td>
    <td>:</td>
    <td>
    <select name="society_id" id="society_id" onChange="get_wing(this.value);">
	<?php //echo $combo_society = $obj_add_member_id->combobox07("select society_id,society_name from society where status='Y' order by society_id desc",$_REQUEST['society_id']); ?>
    </select>
    </td>
</tr>
<?php }?>
<tr align="left">
	<td>Wing</td>
    <td>:</td>
    <td>
    <select name="wing_id" id="wing_id">
	<?php //echo $combo_unit = $obj_add_member_id->combobox07("select wing_id,wing from wing where status='Y' order by wing",$_REQUEST['wing_id']); ?>
    </select>
    </td>
</tr>

<tr align="left">
	<td>Type</td>
    <td>:</td>
    <td>
    <select name="type" id="type">
    	<option value="" <?php //if($_REQUEST['type']==''){echo 'selected';}?>>Please Select</option>
		<option value="Admin" <?php //if($_REQUEST['type']=='Admin'){echo 'selected';}?>>Admin</option>
        <option value="Member" <?php //if($_REQUEST['type']=='Member'){echo 'selected';}?>>Member</option>
    </select>
    </td>
</tr>

<tr align="left">
	<td>Name</td>
    <td>:</td>
    <td><input type="text" name="member_name" value="<?php //echo $_REQUEST['member_name'];?>"/></td>
</tr>

<tr><td colspan="3">&nbsp;</td></tr>

<tr><td colspan="3" align="center"><input type="submit" name="search" id="insert" value="Search"></td></tr>
</table>-->
</form>


<?php }else{ ?>
<form name="add_member_id" id="add_member_id" method="post" action="process/add_member_id.process.php">
	<?php
		$star = "<font color='#FF0000'>*</font>";
		if(isset($_REQUEST['msg']))
		{
			$msg = "Sorry !!! You can't delete it. ( Dependency )";
		}
		else if(isset($_REQUEST['msg1']))
		{
			$msg = "Deleted Successfully.";
		}
		else
		{
			//$msg = '';	
		}
	?>
    <!--<table align='center'>
		<?php
		if(isset($msg))
		{
			if(isset($_POST["ShowData"]))
			{
		?>
				<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php //echo $_POST["ShowData"]; ?></b></font></td></tr>
		<?php
			}
			else
			{
			?>
            	<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php //echo $msg; ?></b></font></td></tr>	   
            <?php		
			}
		}
		else
		{
		?>	
				<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php //echo $_POST["ShowData"]; ?></b></font></td></tr>
        <?php
		}
		?>
        
		<tr align="left">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Select Member</td>
            <td>&nbsp; : &nbsp;</td>
			<td>
            	<select name="com_id" id="com_id" onChange="get_mem_info(this.value);">
					<?php //echo $combo_com_id = $obj_add_member_id->combobox("select member_id,owner_name,society_id from member_main where society_id='".$_SESSION['society_id']."' and status='Y'"); ?>
				</select>
        	</td>
		</tr>
        
		<tr align="left">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Set login Id</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="member_id" id="member_id" /></td>
		</tr>
		
        <tr align="left">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Set Password</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="password" id="password" /></td>
		</tr>
		
        <tr><td colspan="4">&nbsp;</td></tr>
		<tr>
			<td colspan="4" align="center">
            <input type="hidden" name="id" id="id">
            <input type="submit" name="insert" id="insert" value="Update">
            </td>
		</tr>
</table>-->
</form>

<?php } ?>

<table align="center">
<tr>
<td>
<?php
echo "<br>";
$str1 = $obj_add_member_id->pgnation();
echo "<br>";
echo $str = $obj_add_member_id->show($str1);
//echo $str = $obj_add_member_id->display1($str1);
echo "<br>";
//$str1 = $obj_add_member_id->pgnation();
echo "<br>";
?>
</td>
</tr>
<tr>
<td>
<input type="button" id="SendAll" name="SendAll" onClick="CheckSelected()" style="text-align:center;display:block;margin: 1%;font-size: 14px;" align="middle" value="Send to All Selected"/>
</td>
</tr>
<tr>
<td>

</td></tr>
</table>
</div>
<div class="modal fade EmailResponse" role="dialog" aria-hidden="true" style="display: none;">
<div class="modal-dialog">
<div class="modal-content">
 <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          ×
        </button>
        <h4 class="modal-title" id="classModalLabel" style="padding: 3px;">
             Invitation Sent to Member's
            </h4>
      </div>
 <div class="modal-body table-responsive"></div>
  <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">
          Close
        </button>
      </div>
</div>
</div>
</div>
</center>
</div>
<?php include_once "includes/foot.php"; ?>
<div id="openDialogOk" class="modalDialog" >
	<div style="margin:2% auto; ">
		<div id="message_ok"></div>
        <div id="message2"></div>
	</div>
</div>
<script>
$(document).ready(function() {
$('#example').dataTable(
 {
	"bDestroy": true
}).fnDestroy();
var table = $('#example').DataTable();
	table.page.len( -1 ).draw();
});
//$('#example').dataTable( {
	//					dom: 'T<"clear">Blfrtip',
	//					"aLengthMenu": [ [-1], ["All"] ],
	//					aaSorting : [],
	//					columnDefs: 
	//					 [
	//						{
	//							targets: 9,
	//							bSortable: false
	//						}
	//					],
						
					
					
//});
//});	

</script>
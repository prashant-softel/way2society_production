
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>

<title>W2S - Create Group</title>
</head>



<?php include_once "ses_set_s.php";
include_once("includes/head_s.php"); 
include_once("classes/include/dbop.class.php");
include_once("classes/gallery_group.class.php");?> 
<?php
$m_dbConnRoot = new dbop(true);
$obj_gallery=new gallery_group($m_dbConn,$m_dbConnRoot);

$UnitBlock = $_SESSION["unit_blocked"];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>way2society</title>
<link rel="stylesheet" type="text/css" href="css/pagination.css" >
		<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/gallery_group.js"></script>
    <script type="text/javascript" src="js/populateData.js"></script>
    <script language="javascript" type="application/javascript">
	function go_error()
    {
		$(document).ready(function()
		{
			$("#error").fadeIn("slow");
		});
        setTimeout('hide_error()',8000);	
    }
    function hide_error()
    {
		$(document).ready(function()
		{
			$("#error").fadeOut("slow");
		});
    
		//document.getElementById('error').innerHTML = '';
      //  document.getElementById('error').style.display = 'block';
    }
	
	//$( document ).ready(function() {
		var isblocked = '<?php echo $UnitBlock ?>';
		if(isblocked==1)
		{
			//alert("We are sorry,but your access has been blocked for this feature . Please contact your Managing Committee for resolution .");
			
			window.location.href='suspend.php';
		}
    //});
	
	</script>
     <script language="javascript" type="text/javascript">
		function EnableEventType(value)
		{								
			if (value == 1) 
			{				
				$('#create').hide();
				//$('#create').show();
				//$('#view').show();								
			}            
       		
			else(value == 0)
			{									
				$('#create').hide();
				$('#create').hide();
				//$('#').hide();				
			}
		}
		</script>
        <script>
		
		function valgroup()
		{
			
			
		var groupName=document.getElementById('group').value;
		
	//var albumName=document.getElementById('name').value;
//var oFile = document.getElementById('img').files[0];
//alert (groupName);

if(groupName=="")
{
 document.getElementById('ErrorDiv4').style.display='block';
  document.getElementById('ErrorDiv4').innerHTML ="Please Enter Group Name";
  return false; 	 
}
	 
}
	</script>	
 

</head>
<?php 
include_once("includes/head_s.php");
?>
<?php if(isset($_POST['ShowData']) || isset($_REQUEST['msg'])){ ?>
<body onLoad="go_error();">
<?php } ?>
<br>
<div id="middle">
<div class="panel panel-info" id="panel" style="display:block; width:90%;margin-left: 3.5%;">
        <div class="panel-heading" id="pageheader">Create Group</div>
<center>

<?php $star = "<font color='#FF0000'>*</font>";
if(isset($_REQUEST['msg']))
{
	$msg = "Sorry !!! You can't delete it. ( Dependency )";
}
else if(isset($_REQUEST['msg1']))
{
	$msg = "Deleted Successfully.";
}
else{}
?>
<br><br>
<button type="button" class="btn btn-primary btn-circle" onClick="history.go(-1);" style="float:left; margin-left:10px;" id="btnBack"><i class="fa  fa-arrow-left"></i></button>
<form name="group_form" id="group_form" method="post" action="process/gallery_group.process.php" onSubmit="return valgroup();">
<table align='center'>
<?php
if(isset($msg))
{
	if(isset($_POST['ShowData']))
	{
?>
		<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $_POST['ShowData']; ?></b></font></td></tr>
<?php
	}
	else
	{
	?>
		<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $msg; ?></b></font></td></tr>
	<?php
	}
}
else
{
?>
		<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $_POST['ShowData']; ?></b></font></td></tr>
<?php
}
?>

<div id="ErrorDiv4" style="display:block;font-weight:bold; color:#F00;font-size: 12px;"><?php echo $_REQUEST['error4'] ?></div><br>
 <br /><!--<td><input type="button" id="create"  value="Create New" onclick="window.location.href='gallery_group.php'" style="visibility:hidden;"/></td> -->
   <button type="button" id="create"  value="Create New" class="btn btn-primary" onclick="window.location.href='gallery_group.php'" style="visibility:hidden;"/>Create New Group</button>
    <!--<span class="link"><a href="addservicerequest.php">Create New Service Request</a></span> -->
    <br />
    
    </tr>
    <tr><td><br></td></tr>
   
<tr align="left">
            <td valign="middle"><?php echo $star;?></td>
			<td><b>Enter Group Name</b></td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="group" id="group"  value="<?php echo $_REQUEST['group_name'];?>"/></td>
            
                      
            <!--<input type="button" id="upload" value="upload" formenctype="multipart/form-data" >--></td>
    

		</tr>
        <tr><td><br></td></tr>
             <tr>
             <td valign="middle"><?php //echo $star;?></td>
			<td><b>Select Society</b></td>
            <td>&nbsp; : &nbsp;</td>
			<td>
            <div style="overflow-y:scroll;overflow-x:hidden;width:500px; height:200px; border:solid #CCCCCC 2px;" name="society_id[]" id="society_id" onSubmit="return valgroup();" >
           <!--<select name="society_id[]" id="society_id" multiple="multiple" style="overflow-y:scroll;overflow-x:hidden;width:400px; height:200px; border:solid #CCCCCC 2px; " class="dropdown" >
	
--><?php echo $group_create = $obj_gallery->combobox11 ("select `society_id`,`society_name` from `society` where `client_id` = '" . $_SESSION['society_client_id'] . "' AND status='Y' order by society_name ASC ","society_id[]","society_id", $_REQUEST['group_id']); ?>
<!--</select>--></div>
	</td>
   
		</tr>
        
           
        
         <tr><td colspan="4">&nbsp;</td></tr>
        <tr>
			<td colspan="4" align="center">
            <input type="hidden" name="id" id="id"><input type="submit" name="insert" id="insert" value="Insert" style="color: #fff;background-color: #337ab7;border-color: #2e6da4;width:12%;height:20%;"><br><br>
            </td>
		</tr>
</table>
</form>
<center>  
    
 <table align="center">
<tr>
<td>
<?php
echo "<br>";

echo $str1 = $obj_gallery->pgnation();
echo "<br>";
echo $str = $obj_gallery->display1($str1);
echo "<br>";

?>
</td>
</tr>
</table>
</center>
</div>
  </body>
</html>

<?php include_once "includes/foot.php"; ?>
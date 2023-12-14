<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>

<title>W2S - Create New Poll</title>
</head>




<?php
include_once("includes/head_s.php");
include_once ("classes/dbconst.class.php");
include_once("classes/include/dbop.class.php");
include_once("classes/create_poll.class.php");
include_once("classes/include/display_table.class.php");
include_once( "classes/include/fetch_data.php");
$objFetchData = new FetchData($m_dbConn);
$Mobile =$objFetchData->getMobileNumber($_SESSION['unit_id']);
$obj_create_poll = new create_poll($m_dbConnRoot,$m_dbConn);

$UnitBlock = $_SESSION["unit_blocked"];
?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
    <!--<script type="text/javascript" src="js/jquery.min.js"></script>-->
	<script type="text/javascript" src="js/ajax_new.js"></script>
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/create_poll.js"></script>
    <script type="text/javascript" src="ckeditor/ckeditor.js"></script>
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
     <script type="text/javascript">
        $(function()
        {
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker({ 
            dateFormat: "dd-mm-yy", 
            showOn: "both", 
            buttonImage: "images/calendar.gif", 
            buttonImageOnly: true 
        })});
  </script>
 <script type="text/javascript">
var data = new Array(); 

function add_element(){
	if( document.getElementById('options').value.trim().length > 0)
	{
	var bAdd = true;
	for(var i = 0; i < data.length; i++)
	{
		if(data[i] ==  document.getElementById('options').value)
		{
			bAdd = false;
			alert("Value Duplicate");
			break;
		}
	}
	
	if(bAdd == true)
	{
		data.push(document.getElementById('options').value); 
		document.getElementById('options').value='';
	}

disp();
}
}
function remove_element(index_no){
var t1=data.splice(index_no,1);
disp(); 
}
function disp(){
var str='';
var duplicates='';
//str = 'total number of elements in data array : ' + data.length + '<br>';
 for (i=0;i<data.length;i++) 
{ 
		str +=data[i] + " <a  style='text-decoration:none; '  href=# onClick='remove_element("+data.indexOf(data[i])+")'>&nbsp;&nbsp;&nbsp;&nbsp;Remove</a> " + "<br >";  
		
}
document.getElementById('poll_options').value=data.join(",");
document.getElementById('disp').innerHTML=str;
}
</script>

</head>
<body>
<div id="middle">
<div class="panel panel-info" id="panel" style="display:block; margin-top:6%;width:77%;">
        <div class="panel-heading" id="pageheader">Add New Poll</div>
<br>
<center>
    <button type="button" class="btn btn-primary" onClick="window.location.href='poll.php'">Go Back</button>
</center>
<br>
<center>
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
else{}
?>
<?php if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_ADMIN_MEMBER || $_SESSION['role']==ROLE_SUPER_ADMIN))
	
						  {
							  ?>
<div style=" float: right;margin-top: -50px;margin-right: 30px;">
<a href="gallery_group.php" style="text-decoration:none; float:right; font-size:14px;">
                            <button type="button" class="btn btn-info btn-circle" title="Manage Groups"  style="font-size:10px;font-size:1.75vw;width:2vw;height:2vw">
                            <i class="fa fa-group  " style="font-size:10px;font-size:1.25vw"></i>
                           </button>&nbsp;Manage Groups</a></div>
                           
<?php }?>
<center>
<form name="create_poll" id="create_poll" method="post" action="process/create_poll.process.php" enctype="multipart/form-data"  onSubmit="return val();">
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
<tr align="left">
        <td valign="middle"><?php //echo $star;?></td>
        <th><b>Created By </b></th>
        <td>&nbsp; : &nbsp;</td>
          <td id="created_by" ><?php echo $_SESSION['name'];?></td>
	</tr>
    <tr><td colspan="4">&nbsp;</td></tr>
    <tr align="left">
     <td valign="middle"><?php echo $star;?></td>
        <th><b>Select Group</b></th>
        <td>&nbsp; : &nbsp;</td>
<td><select name="group" id="group" style="font-size:12px;">
<?php echo $group_create = $obj_create_poll->combobox("select g.`group_id`, g.`group_name` from `group` as g JOIN `soc_group` as s ON g.`group_id` = s.`group_id` Join `society` as c on s.`society_id` = c.`society_id`  where s.`status`='Y' and g.`status`='Y' and  c.`client_id` = '" .$_SESSION['society_client_id'] . "' and c.society_id='".$_SESSION['society_id']."'", $_REQUEST['group_id']); ?>
</select>
</td>
		<td valign="middle"><?php echo $star;?></td>
        <th><b>Question</b></th>
        <td>&nbsp; : &nbsp;</td>
		<td><input type="text" name="question" id="question" onBlur="SetSMS();" /></td>
</tr>
<tr><td colspan="4">&nbsp;</td></tr>
		<!--<tr align="left">
        <td valign="middle"><?php echo $star;?></td>
        <th><b>Question</b></th>
        <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="question" id="question" /></td>
		</tr>-->
		
        <tr align="left">
        <td valign="middle"><?php echo $star;?></td>
        <th><b>Add Option</b></th>
        <td>&nbsp; : &nbsp;</td>
		<td><input type="text" name="options" id="options" /><input type=button value='Add' onClick='add_element()';> <span style="color:#F00; margin-top: 5px;    padding-left: 10px;"> *May add two options Eg:- 'Yes','No'. </span></td><!--<td  style="color:#F00; float: left; margin-top: 5px;    padding-left: 10px;"> *Min add two options ex:- "Yes" ,"No". </td>-->
           <td valign="middle"></td> 
            <th><b> Send Mobile Notification </b></th>  
            <td>&nbsp;<b>:<b>&nbsp;</td>     
            <td><input type="checkbox" name="mobile_notify" id="mobile_notify" value="1"></td></tr>
        <tr><td colspan="4">&nbsp;</td></tr>
            <tr><td></td><th></th><td></td><td><div id="disp" name="disp"></div><input type="hidden" name="poll_options" id="poll_options" value=""></td>
		</tr>
            
       
    <tr align = "left">
        <td valign="middle"></td>
        <th><b>Attachment</b></th>   
        <td>&nbsp;:&nbsp;</td>               
        <td><input name="fileToUpload" id="fileToUpload" type="file" /> <a id="pollname" style="visibility:hidden;" target="_blank"> View Attachment </a></td>
        <td valign="middle"></td>
        <th><b>Notify By SMS </b></th>   
        <td>&nbsp;:&nbsp;</td>               
        <td><input type="checkbox" name= "PollSMS" id="PollSMS" value="1" onChange="SetSMS();"/><b>&nbsp;&nbsp;  ( Charges Apply)</b>
        <div id="smsTest" name="smsTest" style="display:none;"><br/><textarea name="SMSTemplate" id="SMSTemplate" rows="4" cols="50" style="resize::none;float:left;font:bold;"></textarea><br><input type="button" onClick="showTestSMS()" value="Send Test SMS" style="float: right;margin-right: 40%;margin-top: 3%;background-color: cornflowerblue;color: black;border: none;"></div></td>
    </tr>
   		<tr align="left">
        <td valign="middle"><?php echo $star;?></td>
        <th><b>Start date</b></th>
        <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="start_date" id="start_date"  class="basics" size="10" readonly  style="width:80px;" /></td>
        <td valign="middle"><?php echo $star;?></td>
        <th><b>End date</b></th>
        <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="end_date" id="end_date"  class="basics" size="10" readonly  style="width:80px;" /></td>
		</tr>
        <tr><td colspan="4">&nbsp;</td></tr>
        <!--<tr align="left">
        <td valign="middle"><?php echo $star;?></td>
        <th><b>End date</b></th>
        <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="end_date" id="end_date"  class="basics" size="10" readonly  style="width:80px;" /></td>
		</tr>-->

		<tr align="left">
        <td valign="middle"><?php //echo $star;?></td>
        <th><b>Display Poll Status</b></th>
        <td>&nbsp; : &nbsp;</td>
			<td><select name="poll_status" id="poll_status" style=" width:135px; height:20;" >  
                <OPTION VALUE="1">Without Voting</OPTION>
                <OPTION VALUE="2">After Voting</OPTION> 
                 <OPTION VALUE="3">After End</OPTION>                
             </select></td>
             
        <td valign="left"></td> 
            <td><b> Allow Revote </b></td>  
            <td>&nbsp;:&nbsp;</td>     
            <td><input type="checkbox" name="revote" id="revote" value="1">   </td>
		</tr>
     
            <tr><td colspan="4">&nbsp;</td></tr>
        <!--<tr align="left">
            <td valign="left"></td> 
            <td><b> Allow Revote </b></td>  
            <td>&nbsp;:&nbsp;</td>     
            <td><input type="checkbox" name="revote" id="revote" value="1">   </td>
            </tr>-->
            <tr align="left">
    	<td valign="middle"><?php //echo $star;?></td>
    	<td style="text-align:left;"><b>Additional Content</b></td>
   		<td>&nbsp; : &nbsp;</td>
        <td colspan="5"><textarea name="additional_content" id="additional_content" rows="5" cols="50"></textarea></td>
    </tr>
    <tr><td><br></td></tr>

    	<tr>
    	<td valign="middle"></td>
        <td style="text-align:left;"><b>Question for comment?</b></td>
        <td>&nbsp; : &nbsp;</td>
        
         <td><input type="checkbox" name="chkComment" id="chkComment" value="1" checked style="float: right;margin-right: 90%;background-color: cornflowerblue;color: black;border: none;"><span style="color:#F00; margin-top: 5px;padding-left: 10px;"> *Just Uncheck if you dont want to add question.* </span> </td>
         
         </tr>
         
         <tr>
        <td valign="middle"></td>
        <td style="text-align:left;"><label id="title">Title</label></td>
        <td>&nbsp;  &nbsp;</td>
	<td><textarea name="comment_desc" id="comment_desc" rows="3" cols="50" style="margin-right: 90%;margin-top:5%"></textarea></td>
    
         
    </tr>
    <script type="text/javascript">
        var cb = document.getElementById('chkComment'); // put your id's here
        var tb = document.getElementById('comment_desc');
		var lb=document.getElementById('title');
		 
        cb.onchange = function() { // listen for event change
        if(!cb.checked) { // check state
            tb.style.visibility = 'hidden';
			lb.style.visibility = 'hidden';
        }
        else {
           tb.style.visibility = 'visible';
		   lb.style.visibility=  'visible';
         }
     }
   </script>
   
    <!--<tr><td colspan="4"><textarea name="additional_content" id="additional_content" rows="5" cols="50"></textarea></td></tr>-->
       	<script>			
			CKEDITOR.config.extraPlugins = 'justify';
			CKEDITOR.replace('additional_content', {toolbar: [
         						{ name: 'clipboard', items: ['Undo', 'Redo']},{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align'], items: [ 'NumberedList', 'BulletedList','JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
        						{name: 'editing', items: ['Format', 'Bold', 'Italic', 'Underline', 'Strike'] }
   								 ],
								 height: 250,
        						 width: 500,
								 uiColor: '#14B8C4'
								 });
		</script>
    </tr>
 <tr><td colspan="4"><input type="hidden" name="poll_id" id="poll_id" value="<?php //echo $_SESSION['name'];?>"> </td></tr>
        <tr><td colspan="4">&nbsp;</td></tr>
		<tr>
			<td colspan="8" align="center">
            <input type="hidden" name="id" id="id">
            <input type="hidden" id="userMobileNo" name="userMobileNo" value="<?php echo $Mobile[0]['mob'] ?>">
 			<?php if($_REQUEST['edit'] <> 0)
			{?><input type="hidden" id="updateid" name="updateid" value="<?php echo $_REQUEST['edit']; ?>">
               <input type="hidden" id="OgPollQuestion" name="OgPollQuestion">
			<?php }else
			{?>
            <input type="hidden" id="updateid" name="updateid" value="0">
			<?php 	}?>
            <input type="submit" name="insert" id="insert" value="Submit" class="btn btn-primary" style="color:#FFF; width:100px;background-color:#337ab7;" >
            </td>
		</tr>
	<tr><td colspan="4">&nbsp;</td></tr>	
</table>
</form>
</center></div>
<table align="center">
<tr>
<td>
<?php
//echo "<br>";
//$str1 = $obj_create_poll->pgnation();
//echo "<br>";
//echo $str = $obj_create_poll->display1($str1);
//echo "<br>";
//$str1 = $obj_create_poll->pgnation();
//echo "<br>";
?>
</td>
</tr>
</table>
</div>
</body>
</html>
<?php
	if(isset($_REQUEST['edit']) && $_REQUEST['edit'] <> '')
	{
		?>
			<script>
				getPollService('edit-' + <?php echo $_REQUEST['edit'];?>);				
			</script>
		<?php
	}
	
	if(isset($_REQUEST['deleteid']) && $_REQUEST['deleteid'] <> '')
	{
		?>
			<script>
				getPollService('delete-' + <?php echo $_REQUEST['deleteid'];?>);				
			</script>
		<?php
	}
?>

<?php include_once "includes/foot.php"; ?>

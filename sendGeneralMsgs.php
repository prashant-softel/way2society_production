<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - General Messages</title>
</head>
<?php include_once("includes/head_s.php");
include_once("classes/dbconst.class.php");
include_once("classes/sendMsg.class.php");
include_once( "classes/include/fetch_data.php");
$objFetchData = new FetchData($m_dbConn);
$obj_notify = new notification($m_dbConn,$m_dbConnRoot);
$Mobile =$objFetchData->getMobileNumber($_SESSION['unit_id']);
?>
 
<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	
    <script type="text/javascript" src="js/populateData.js"></script>
    <script type="text/javascript" src="js/notification10112018.js"></script>
    <!--<script type="text/javascript" src="ckeditor/ckeditor.js"></script>-->
	<script language="javascript" type="application/javascript">
	
	function go_error()
    {
        setTimeout('hide_error()',10000);	
    }
		
    function hide_error()
    {
		document.getElementById('error').innerHTML = '';
        document.getElementById('error').style.display = 'none';	
    }
	
	function limitText(limitField, limitCount, limitNum) {
		if (limitField.value.length > limitNum) {
			limitField.value = limitField.value.substring(0, limitNum);
		} else {
			limitCount.value = limitNum - limitField.value.length;
		}
	}
	
	function check_words()
	{
		var text = document.getElementById('description').value;
		var array = text.split(" ");
		var restricted_words = ["property","flat"];
		var found = new Array();
		for(var i = 0; i < array.length; i++)
		{
			for(var j = 0; j < restricted_words.length; j++)
			{
				if(array[i].toLowerCase() == restricted_words[j])
				{
					found.push(array[i]);
				}
			}
		}
		if(found != '')
		{
			alert("'" + found + "' word(s) are not allowed in SMS text.");
		}
	}
	
	$(document).ready(function()
	{
		$('#description').bind('keypress', function(e) {
			if($('#description').val().length >= 0)
			{
				var k = e.keyCode;
			    var ok = k >= 65 && k <= 90 || // A-Z
			             k >= 97 && k <= 122 || // a-z
			             k >= 48 && k <= 57 || // 0-9
			             k == 32; // {space}

			    if (!ok)
				{
					e.preventDefault();
			    }
			}
		});
	});	
	</script>
    <style type="text/css">
 
  /*table.cruises { 
    font-family: verdana, arial, helvetica, sans-serif;
    font-size: 11px;
    cellspacing: 0; 
    border-collapse: collapse; 
    width: 535px;    
    }*/
  table.cruises td { 
    border-left: 1px solid #999; 
    border-top: 1px solid #999;  
    padding: 2px 4px;
    }
  table.cruises tr:first-child td {
    border-top: none;
  }
/*  table.cruises th { 
    border-left: 1px solid #999; 
    padding: 2px 4px;
    background: #6b6164;
    color: white;
    font-variant: small-caps;
    }*/
  table.cruises td { background: #eee; overflow: hidden; }
  
  div.scrollableContainer { 
    position: relative; 
    padding-top: 6em;
	width:100%;
    margin: 0px; 
	border: 1px solid #999;   
   }
  div.scrollingArea { 
    height: 600px; 
    overflow: auto; 
    }

 /* table.scrollable thead{
    left: -1px; top: 0;
    position: absolute;
    }*/
</style>
</head>

<?php if(isset($_REQUEST['ShowData']) || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])){ ?>
<body onLoad="go_error();">
<?php }else{ ?>
<body>
<?php } ?>


<div class="panel panel-info" id="panel" style="display:none">
	<div class="panel-heading" id="pageheader">General Messages</div>

<center>
    <br>
    <table>
    	<tr>
    		<td>
				<button type="button" class="btn btn-primary" onClick="window.open('SMSCounterHistory.php');"  style="margin-bottom: 10px;"><i class="fa  fa-history">&nbsp;SMS Counter History</i></button>
    		</td>
            <td>
				<input type="button" onClick="window.open('generalSMSHistory_new.php','SMS History','_blank')"  value="View SMS History" class="btn btn-primary"  style="color:#FFF;  box-shadow:none;border-radius: 5px; width:10vw; height:30px;background-color: #337ab7;border-color: #2e6da4; "   />
             </td>
    	</tr>
    </table>
<div id="maintenance_bill">
<form name="sendMsg" id="sendMsg" method="post" action="sendGeneralMsgs.php" <?php echo $val;?>>	   
    <table align='center' cellspacing="8px" style="border:1px solid black; width:100%">
		<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $_REQUEST["ShowData"]; ?></b></font></td></tr>	        
        <tr align="left" style="bo">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Wing</td>
            <td>&nbsp; : &nbsp;</td>
			<td>
                <select name="wing_id" id="wing_id" style="width:142px;" onChange="get_unit(this.value);">
                    <?php echo $combo_wing = $obj_notify->combobox("select wing_id,wing from wing where status='Y' and society_id = '" . DEFAULT_SOCIETY . "'", $_REQUEST['wing_id'], 'All', '0'); ?>
				</select>
            </td>   
         
            <td align="left" valign="middle"></td>
            <td>Message Text</td>
            <td>&nbsp; : &nbsp;</td>
            <td rowspan="2"><div style="width: 307px;"><textarea name="description" id="description" value=" " rows="5" cols="50" onKeyDown="limitText(this.form.description,this.form.countdown,152);" 
                    onKeyUp="limitText(this.form.description,this.form.countdown,152);" onBlur="check_words();"></textarea></div><br>
               <font size="1" color="#CC0000">Note :Please replace text {#var#} of Message Text</font><br>    
                    <font size="1" color="#CC0000">
                You have <input type="text"  name="countdown" id="countdown" size="3" value="152" style="width:35px;text-align:center;" readonly /> characters left.
                </font><br>
            </td>
		</tr>
        
		<tr align="left" style="padding-bottom:10px;">
        	<td valign="middle"><?php if(isset($_GET['ws'])){echo $star;}?></td>
			<td>Unit No. ( Flat No )</td>
            <td>&nbsp; : &nbsp;</td>
			<td>
                <select name="unit_id" id="unit_id" style="width:142px;">
                	<?php echo $combo_unit = $obj_notify->combobox("select unit_id, unit_no from unit where wing_id = '" . $_REQUEST['wing_id'] . "'", $_REQUEST['unit_id'], "All", '0');
					?>
				</select>
            </td>
            <td></td>
		</tr>
        <tr align="left" style="padding-bottom:10px;">
        	<td valign="middle"></td>
			<td>SMS Template</td>
            <td>&nbsp; : &nbsp;</td>
			<td>
                <select name="template_id" id="template_id" onchange="smsSet()" style="width:142px;">
                	<?php echo $combo_sms = $obj_notify->combobox1("select id, Template_name from general_sms_templates where client_id = '" . $_SESSION['client_id'] . "'", '0');
					?>
				</select>
            </td>
            <td></td>
		</tr>        
    	<tr>
      <!--  	<td colspan="4" style="text-align:center">
        <input type="submit" name="insert" id="insert" value="Fetch Units"  class="btn btn-primary"  style="color:#FFF;  box-shadow:none;border-radius: 5px; width:10vw; height:30px;background-color: #337ab7;border-color: #2e6da4; " />    </td>-->
        
			<td colspan="4"></td>
			<td colspan="4"><div id="notice" style="font-size:0.8vw; text-align:center"></div></td>
        </tr>
        <tr>
        <td colspan="4" style="text-align:center;">
        <input type="submit" name="insert" id="insert" value="Fetch Units"  class="btn btn-primary"  style="color:#FFF;  box-shadow:none;border-radius: 5px; width:10vw; height:30px;background-color: #337ab7;border-color: #2e6da4;margin-left:25%; " /></td>
        
        <?php if($_SESSION['feature'][CLIENT_FEATURE_SMS_MODULE] == 1){?>
        <td colspan="4" style="text-align:center">
        <input type="button" name="insert" id="insert" value="Send Test SMS"  class="btn btn-primary"  style="color:#FFF;  box-shadow:none;border-radius: 5px; width:10vw; height:30px;background-color: #337ab7;border-color: #2e6da4; " onClick="SendTest();" /></td>
     <?php }
		else
		{?>
        <td colspan="4" style="text-align:center">
         <input type="button"  name="insert" id="insert" value="Send Test SMS"  title="Your Not Subscribe For SMS"  style="color:black;  width:10vw; height:30px;background: lightgray; " disabled /></td>
		<?php } ?>
        </td>
        </tr>
        <tr> </tr>
        <tr>
        <td colspan="12">
        <?php if(isset($_REQUEST['wing_id']))
		{?> 
		<br><div style="padding-left:30%"><?php if($_SESSION['feature'][CLIENT_FEATURE_SMS_MODULE] == 1){?>
<!--			<input type="button" value="Send SMS To All Selected Units" onclick="GeneralSMSSentAll();" />	
            <input type="button" value="Send Mobile Notification To All Selected Units"  onclick="sendMobileNotificationAllSelected();" />-->
            
            <input type='checkbox' id='btn_all_sms'/>&nbsp;&nbsp;&nbsp;&nbsp;
      		<label disabled/>Send SMS To Selected Units</label>&nbsp;&nbsp;&nbsp;&nbsp;
            <input type='checkbox'  id='btn_all_notification'/>&nbsp;&nbsp;&nbsp;&nbsp;
            <label>Send Mobile Notification To Selected Units</label>&nbsp;&nbsp;&nbsp;&nbsp;	
			<?php }
			else
			{?>
             <div style="padding-left:10%">	
             <input type='checkbox'  id='btn_all_notification' disabled />&nbsp;&nbsp;&nbsp;&nbsp;
             <label>Your Not Subscribe For SMS</label>
             </div>	
            <?php } ?> </div><div style="padding-left:40%;"><BR>
		 
         <input type="submit" name="SendNotification" id="SendNotification" value="Send Notification"  class="btn btn-primary"  style="color:#FFF;  box-shadow:none;border-radius: 5px; width:10vw;background-color: #337ab7;border-color: #2e6da4; " onclick ="CheckRequestedNotification(true)"/>
         <?php }?></div></td>
         </tr>
        <!--<tr align="left">
        	<td valign="middle"></td>
            <td>Message Text</td>
            <td>&nbsp; : &nbsp;</td>
            <td><div style="float:left;width: 307px;"><textarea name="description" id="description" rows="5" cols="50" onKeyDown="limitText(this.form.description,this.form.countdown,152);" 
					onKeyUp="limitText(tshis.form.description,this.form.countdown,152);"></textarea></div><div id="notice" style="float:left;margin-top: 10px;width: 291px; font-size:0.8vw;"></div></td>-->
            <!-- <td  id="notice"></td>       
    	</tr>                --> 
       	<script>			
			/*CKEDITOR.config.extraPlugins = 'justify';
			CKEDITOR.replace('description', {toolbar: [
         						{ name: 'clipboard', items: ['Undo', 'Redo']},{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align'], items: [ 'NumberedList', 'BulletedList','JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
        						{name: 'editing', items: ['Format', 'Bold', 'Italic', 'Underline', 'Strike'] }
   								 ],
								 height: 100,
        						 width: 700,
								 uiColor: '#14B8C4'
								 });*/
		</script>
       
        <!--<tr><td colspan="4">&nbsp;</td></tr>
		
        <tr>
			<td colspan="4" align="center">
        	    <input type="submit" name="insert" id="insert" value="Fetch Units"  class="btn btn-primary"  style="color:#FFF;  box-shadow:none;border-radius: 5px; width:100px; height:30px;background-color: #337ab7;border-color: #2e6da4; "/>
		            	    <input type="submit" name="insert" id="insert" value="Fetch Units"  class="btn btn-primary"  style="color:#FFF;  box-shadow:none;border-radius: 5px; width:10vw; height:30px;background-color: #337ab7;border-color: #2e6da4; " /> 
		    <input type="button" onClick="window.open('generalSMSHistory_new.php','SMS History','_blank')"  value="View SMS History" class="btn btn-primary"  style="color:#FFF;  box-shadow:none;border-radius: 5px; width:10vw; height:30px;background-color: #337ab7;border-color: #2e6da4; "   />             
            </td>
		</tr>-->
	</table>
</form>


<input type="hidden" id="userMobileNo" name="userMobileNo" value="<?php echo $Mobile[0]['mob'] ?>">
<table align="center" style="width:100%">
<tr> 
<td align="center">
<?php

if(isset($_REQUEST['wing_id']))
{
	echo "<br>";
	if(isset($_REQUEST['description']) && $_REQUEST['description'] <> "")
	{
?>
	<script>
		document.getElementById('description').value = '<?php echo $_REQUEST['description']; ?>';
	</script>
<?php  
	}
	$str1 = $obj_notify->pgnation($_SESSION['society_id'], $_REQUEST['wing_id'], $_REQUEST['unit_id']);	
	echo "<br>";
}
?>
</td>
</tr>
</table>
</center>
</div>
<script>
document.getElementById('notice').innerHTML = "Note :  <b>" + newstring + "</b><br> All these words/characters are not allowed in SMS Text.";

 var Description=document.getElementById('description').disabled=true;
 
  
function smsSet ()
{
   var Id=document.getElementById('template_id').value;
   $.ajax({
    url:"ajax/sendGeneralMsgs.ajax.php",
    method:"post",
    data:{'smsSet':'smsSet', 'method':'sendMsg_id','Id':Id},
    success:function(data)
    {
        console.log(data);        
       var desccount= document.getElementById('description').value=data.trim();
       
       if(desccount.length==0)
      {
         //alert("null");
         document.getElementById('description').disabled=true;
         console.log(desccount.length);
      }
     else
      {  
        //alert("not null");
        document.getElementById('description').disabled=false;
        console.log(desccount.length);
        document.getElementById('countdown').value=desccount.length;
      }
    }
  });
}
</script>
<br><br><br><br><br><br><br><br><p></p>
<?php include_once "includes/foot.php"; ?>

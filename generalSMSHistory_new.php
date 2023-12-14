<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - SMS Delivery Report</title>
</head>
<?php 
if(isset($_SESSION['admin']))
{
	include_once("includes/header.php");
}
else
{
	include_once("includes/head_s.php");
}
include_once("classes/dbconst.class.php");
include_once("classes/SmsHistory.class.php");
include_once("classes/include/dbop.class.php");
$m_dbConnRoot = new dbop(true);
$m_ObjsmsHistory = new SmsHistory($m_dbConn,$m_dbConnRoot );
?>	
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
	<div class="panel panel-info" id="panel" style="display:none;">
    	<div class="panel-heading" id="pageheader">SMS Delivery Report</div>
       <div align="center">
            <br/> <br/>
     <center>        
<form name="filter" id="filter" action="" method="post" >
	<table style="width:60%; border:1px solid black; background-color:transparent;border-radius:10px;">
    <tr> <td colspan="3"><br/> </td></tr>
    	<tr>
        	<td> &nbsp; Sms Type : 
            <select name="sms_type" id="sms_type" style="width:150px;" value="<?php echo $_REQUEST['sms_type'];?>">
                	<option value="0"  >All</option>
                    <option value="1" <?php echo$_REQUEST['sms_type'] == "1"?'selected':'';?>>General Sms</option>
                    <option value="2" <?php echo$_REQUEST['sms_type'] == "2"?'selected':'';?>>Bill Reminder Sms</option>
                    <option value="3" <?php echo$_REQUEST['sms_type'] == "3"?'selected':'';?>>Bill Notification Sms</option>
                </select>
   			&nbsp;&nbsp;
             From :<input type="text" name="from_date" id="from_date"  class="basics" size="10" style="width:80px;" value = "<?php echo $_REQUEST['from_date'];?>"/> To :<input type="text" name="to_date" id="to_date"  class="basics" size="10" style="width:80px;"  value = "<?php echo $_REQUEST['to_date'];?>"/>
           &nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="submit" value="Submit" class="btn btn-primary"  style="padding: 6px 12px; color:#fff;background-color: #2e6da4;" /> </td>
     	</tr>        
        <tr> <td colspan="3"><br/> </td></tr>
    </table>
</form>
</center>
<br/>
</div>  
			<?php
		
            if(isset($_REQUEST) && sizeof($_REQUEST) > 0)
			{
				$m_ObjsmsHistory->pgnation($_REQUEST);
			}
			else
			{
				$pgarray =  array();
				$res =  $m_ObjsmsHistory->pgnation($pgarray);
				if($res == "")
				{
         			echo $res;
				}
				else
				{
					echo "<center><br/><br/><h5>There are no recent General SMS(s). <a href='sendGeneralMsgs.php'> Click here </a>to start sending General SMS.</h5></center>";
				}
			}
				
            ?>
            <script>
                function GetDeliveryStatus(msgID, ID,SentSMSManually,smsType)
                {         
					$.ajax({
                        url : "classes/generalSms.class.php",
                        type : "POST",
                        data: { "GetDeliveryReport":1, "MessageID":msgID, "TableID":ID,"SentSMSManually":SentSMSManually,"Type":smsType} ,
                        success : function(data)
                        {	  
							//alert(data);                          
							location.reload(true);                       
                        },
                            
                        fail: function()
                        {
                            alert("falied");
                        },
                        
                        error: function(XMLHttpRequest, textStatus, errorThrown) 
                        {
                        }
                    });
                }
				
			
            </script>
            
            
<?php include_once "includes/foot.php"; ?>            
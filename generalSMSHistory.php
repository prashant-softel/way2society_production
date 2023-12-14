
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
<title>W2S - General Message Delivery Report</title>
</head>




<?php include_once("includes/head_s.php");
	include_once("classes/include/display_table.class.php");
	$display_pg=new display_table($m_dbConn);
?>			
	<div class="panel panel-info" id="panel" style="display:none;">
    	<div class="panel-heading" id="pageheader">General Message Delivery Report</div>  
			<?php 
            
            $LoginQuery = "SELECT `login_id`,`member_id` FROM `login`";
            $Details = $m_dbConnRoot->select($LoginQuery);
            $loginArr = array();
            for($cnt = 0; $cnt < sizeof($Details); $cnt++)
            {
                $loginArr[$Details[$cnt]['login_id']] = $Details[$cnt]['member_id'];
            }
			
			$memberQuery = "SELECT `unit`, `owner_name` FROM `member_main`";
			$memberDetails = $m_dbConn->select($memberQuery);
			$memberArr = array();
            for($cnt = 0; $cnt < sizeof($memberDetails); $cnt++)
            {
                $memberArr[$memberDetails[$cnt]['unit']] = $memberDetails[$cnt]['owner_name'];
            }
            
            //$sql = 'SELECT gs.ID, mm.owner_name,gs.SentGeneralSMSDate,gs.MessageText,gs.SentBy,gs.SentReport,gs.status,gs.DeliveryStatus FROM `generalsms_log` AS gs JOIN `member_main` AS mm ON gs.UnitID = mm.unit';
			 $sql = 'SELECT gs.ID, gs.UnitID, gs.SentGeneralSMSDate,gs.MessageText,gs.SentBy,gs.SentReport,gs.status,gs.DeliveryStatus,gs.DeliveryReport FROM `generalsms_log` AS gs';           
			 $history = $m_dbConn->select($sql);
			if(sizeof($history) > 0)
			{
				for($i = 0; $i < sizeof($history); $i++)
				{
					$messageReport = explode(',',$history[$i]['SentReport']);
					$DeliveryReport = explode(',',$history[$i]['DeliveryReport']);
					
					if (stripos($history[$i]['DeliveryStatus'], 'Deliv') !== false) 
					{
						$history[$i]['DeliveryReport']= $DeliveryReport[5];
					}
					else
					{
						$history[$i]['DeliveryReport']= "";
					}
					if($history[$i]['SentReport'] == "")
					{
						$history[$i]['View'] = "";
					}
				   // else if (stripos($history[$i]['DeliveryStatus'], 'Deliv') !== false) {
					else if($history[$i]['DeliveryStatus'] != "") {
						$history[$i]['View'] = "";
					}
					else
					{					
						$history[$i]['View'] = '<a onClick="GetDeliveryStatus(' . $messageReport[2] . ','.$history[$i]['ID'].')" style="cursor:pointer;">Refresh Status</a>';
					}
					$history[$i]['SentBy'] = $loginArr[$history[$i]['SentBy']];
					$history[$i]['UnitID'] = $memberArr[$history[$i]['UnitID']];
					unset($history[$i]['SentReport']);		
				}
			}
            if(isset($history))
			{
				$thheader = array('Member Name','Sending Time','Message Text','Sent By', 'Status', 'Delivery Status','Delivery Time');
				$display_pg->th		= $thheader;	
				$res = $display_pg->display_datatable($history, false, false);
			}
			else
			{
				echo "<center><br/><br/><h5>There are no recent General SMS(s). <a href='sendGeneralMsgs.php'> Click here </a>to start sending General SMS.</h5></center>";
			}
            ?>
            <script>
                function GetDeliveryStatus(msgID, ID)
                {         
					var SentSMSManually =  1;       
                    $.ajax({
                        url : "classes/generalSms.class.php",
                        type : "POST",
                        data: { "GetDeliveryReport":1, "MessageID":msgID, "TableID":ID,"SentSMSManually":SentSMSManually,"Type":0} ,
                        success : function(data)
                        {	  
							//alert(data);                          
							//location.reload(true);                       
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
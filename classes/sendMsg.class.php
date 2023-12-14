<?php if(!isset($_SESSION)){ session_start(); }
include_once("include/display_table.class.php");
include_once("utility.class.php");
include_once("dbconst.class.php");

class notification //extends dbop
{
	public $actionPage="../sendGeneralMsgs.php";
	public $m_dbConn;
	public $m_dbConnRoot;
	public $obj_utility;
	
	function __construct($dbConn,$dbConnRoot)
	{
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = $dbConnRoot;
		$this->display_pg=new display_table($this->m_dbConn);
		$this->obj_utility = new utility($this->m_dbConn);
	}
	
	public function startProcess()
	{
		$errorExists=0;
		
		if($_REQUEST['insert']=='Insert' && $errorExists==0)
		{
			
		}
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			
		}
		else
		{
			echo "<script>alert('error');</script>";
			return $errString;
		}
	}
	
	public function pgnation($society_id, $wing_id, $unit_id)
	{		
		$todayDate = date("Y-m-d"); //date("Y-m-d")
		$memberIDS = $this->obj_utility->getMemberIDs($todayDate);	
		//$memberIDS = $this->obj_utility->getMemberIDs($_SESSION['default_year_end_date']);	
		$sql1 = 'select unittbl.unit_id, unittbl.unit_no, wingtbl.wing, societytbl.society_name, societytbl.society_code, membertbl.owner_name, membertbl.mob, membertbl.email from unit as unittbl JOIN wing as wingtbl on unittbl.wing_id = wingtbl.wing_id JOIN society as societytbl on unittbl.society_id = societytbl.society_id JOIN member_main as membertbl ON membertbl.unit = unittbl.unit_id where societytbl.society_id = "' . $society_id . '"  and membertbl.member_id IN ('.$memberIDS.') '; 
		
		if($wing_id <> 0)
		{
			$sql1 .= ' and unittbl.wing_id = "' . $wing_id . '"';
		}		
		if($unit_id <> 0)
		{
			$sql1 .= ' and unittbl.unit_id = "' . $unit_id . '"';
		}
		
		$sql1 .= ' Group BY unittbl.unit_id ORDER BY unittbl.sort_order ASC';
		$result = $this->m_dbConn->select($sql1);
		
		$this->show_unit($result);
	}		
	
	public function show_unit($res)
	{			
		if($res<>"")
		{
			$str_unit_ary = '';						
			$iCounter = 1;
		?>   
        	  
 			<div class="scrollableContainer">
 			<div class="scrollingArea">	
            <table align="center" class="display" cellspacing="0" width="100%" id="example">
			<thead style="left: -1px; top: 0;position: absolute;">
				<tr height="30">
					<th><input type="checkbox" id="chk_all" onclick="SelectAll(this);"</th>
					<th width="50">Sr No</th>
					<th width="400">Member Name</th>
					<th width="30">Wing</th>
					<th width="50">Unit No.</th>					
					<th width="60">Mobile No.</th>                   					
					<th width="200">Notification</th>											  
				</tr>
			</thead>
            
			<tbody>
     
        <?php foreach($res as $k => $v)
		{
			$str_unit_ary .= $res[$k]['unit_id'] . '#';
		?>
        
        	<tr height="25" align="center">
			<td><input type="checkbox" value="1" id="chk_<?php echo $res[$k]['unit_id']; ?>" /></td>	
        	<td align="center"><?php echo $iCounter++;?></td>
            <td align="left"><?php echo $res[$k]['owner_name'];?></td>
            <td align="center"><?php echo $res[$k]['wing'];?></td>
            <td align="right"><?php echo $res[$k]['unit_no'];?></td>            
            <td align="right"><?php echo $res[$k]['mob'];?></td>                         						                                    
           	<td align="center">
			<?php if($_SESSION['feature'][CLIENT_FEATURE_SMS_MODULE] == 1){?>
				<input type="button" id="send_sms" value="Send SMS" onclick="sendGeneralSMS(<?php echo $res[$k]['unit_id']; ?>);" />
				<iframe src="" name="sendsms_<?php echo $res[$k]['unit_id']; ?>" id="sendsms_<?php echo $res[$k]['unit_id']; ?>" style="border:0px solid #0F0;width:0px;height:0px;"></iframe>
			<?php }
				else
				{?>
                	 <input type="button" id="send_sms" value="Send SMS"  style="background: lightgray" title="Your Not Subscribe For SMS"  disabled/>
            <?php } ?>      
            </td>
            	<td>
            			<input type="button" id=id="send_android_notification_<?php echo $res[$k]['unit_id']; ?>" onclick="sendMobileNotification(<?php echo $res[$k]['unit_id']; ?>, <?php echo $_SESSION['society_id']; ?>, '<?php echo $res[$k]['email'] ?>');" value="Send Android Notification"/>
            		</td>
            	<?php
            ?>
            <td align="center">
            	<div id="status_<?php echo $res[$k]['unit_id']; ?>" style="color:#0033FF; font-weight:bold;"></div>
            </td>
            </tr>
        <?php 
		}
		?>
        
        </tbody>
       
        </table
			  
        ></table>
		<input type="hidden" id="unit_ary" value="<?php echo $str_unit_ary; ?>" />
		<input type="hidden" id="society_id" value="<?php echo $_SESSION['society_id']; ?>" />
       	<input type="hidden" id="SendGeneralNotification" value="1" />
       
		<br />		
		<?php if($_SESSION['feature'][CLIENT_FEATURE_SMS_MODULE] == 1){?>
				<!--<input type="button" value="Send SMS To All Selected Units" onclick="GeneralSMSSentAll();" />-->	
			<?php }
				else
				{?>
                	  <!--<input type="button" value="Send SMS To All Selected Units"  title="Your Not Subscribe For SMS"  disabled />	-->
            <?php } ?>      
            	
		<br /><br />
		<?php
		}
		else
		{			
			?>
            <table align="center" border="0">
            <tr>
            	<td><font color="#FF0000" size="2"><b>No Records Found.</b></font></td>
            </tr>
            </table>
           
            <?php		
		}?>
         </div></div>
	<?php }
	
	public function combobox($query,$id, $defaultText = 'Please Select', $defaultValue = '')
	{
		$str = '';
		
		if($defaultText != '')
		{
			$str .= "<option value='" . $defaultValue . "'>" . $defaultText . "</option>";
		}
		
		$data = $this->m_dbConn->select($query);
		if(!is_null($data))
		{
			foreach($data as $key => $value)
			{
				$i=0;
				foreach($value as $k => $v)
				{
					if($i==0)
					{
						if($id==$v)
						{
							$sel = 'selected';
						}
						else
						{
							$sel = '';
						}
						
						$str.="<OPTION VALUE=".$v.' '.$sel.">";
					}
					else
					{
						$str.=$v."</OPTION>";
					}
					$i++;
				}
			}
		}
		return $str;
	}	
	public function combobox1($query,$id)
	{
		echo $query;
		$str = '';
		
		$defaultText="Please select";
		$defaultValue = 0;
		if($defaultText != '')
		{
			$str .= "<option value='" . $defaultValue . "'>" . $defaultText . "</option>";
		}
		
		$data = $this->m_dbConnRoot->select($query);
		if(!is_null($data))
		{
			foreach($data as $key => $value)
			{
				$i=0;
				foreach($value as $k => $v)
				{
					if($i==0)
					{
						if($id==$v)
						{
							$sel = 'selected';
						}
						else
						{
							$sel = '';
						}
						
						$str.="<OPTION VALUE=".$v.' '.$sel.">";
					}
					else
					{
						$str.=$v."</OPTION>";
					}
					$i++;
				}
			}
		}
		return $str;
	}	

  public function  sendMsg_id($id)
  {

  	$query="select id, sms_text from general_sms_templates where client_id = '" . $_SESSION['client_id'] . "' and id ='".$id."'";
      	$data = $this->m_dbConnRoot->select($query);
        $Sendmsg=$data[0]['sms_text'];
        return $Sendmsg;
  }


}
?>
<?php
include_once("dbconst.class.php");
include_once("include/display_table.class.php");
include_once("utility.class.php");

class SMSReminder extends dbop
{
	public $m_dbConn;
	public $m_dbConnRoot;
	public $obj_utility;
	function __construct($dbConn, $dbConnRoot)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg=new display_table($this->m_dbConn);
		$this->m_dbConnRoot = $dbConnRoot;
		$this->obj_utility = new utility($this->m_dbConn, $this->m_dbConnRoot);
		//dbop::__construct();
	}
	public function displaysms($res)
	{?>
		<table id="example" class="display" cellspacing="0">
        <?php if($res<>"")
		{?>
        	<thead>
            <tr height="30" bgcolor="#FFFFFF">
            <th style="text-align:center">Sr.No</th>
            <th style="text-align:center">Event Date</th> 
            <th style="text-align:center">Event Reminder Date</th>  
            <th style="text-align:center">Reminder Category</th>
            <th style="text-align:center">Reminder</th> 
            <th style="text-align:center">SMS</th>
            <th style="text-align:center">EMAIL</th> 
            <th style="text-align:center">MOBILE_NOTIFY</th> 
              <?php if($this->obj_utility->checkAccess()=="0")
					{
					?>
                	   <th style="text-align:center">Edit</th>
            		   <th style="text-align:center">Delete</th> 
                    <?php
					}
					?>         
            </tr>
            </thead>
            <tbody>
             <?php $k=1;
			 for($i=0;$i<sizeof($res);$i++){ ?>
            	<tr height="25" bgcolor="#BDD8F4" align="center"> 
                    	<?php
						if($this->obj_utility->checkAccess()=="0")
						{
						?>
                        	<td align="center"><?php echo $k++;?></td>
                        	<td align="center"><?php echo getDisplayFormatDate($res[$i]['EventDate']);?></td>
                            <td align="center"><?php if(getDisplayFormatDate($res[$i]['rem_before'])<>''){ ?>
								<?php echo getDisplayFormatDate($res[$i]['rem_before']);?></td>
                                <?php }else{
									echo getDisplayFormatDate($res[$i]['EventDate']);
								}
									?>
                                

							<td align="center"><?php if($res[$i]['rem_type']==0)
														{ 
															echo 'System Generated';
														}
														else
														{
															echo "User Defined";
														}
														?></td>
                            <td align="center"><?php switch ($res[$i]['ReminderType']){
								case 1:
									echo "Bill Reminder";
									break;
								case 2:
									echo "Event Reminder";
									break;
								case 3:
									echo "FD Maturity Reminder";
									break;
								default:
									$status="Y";
									$sql="select title from customer_reminder where id='".$res[$i]['rem_id']."'";
									$result=$this->m_dbConnRoot->select($sql);
									$title=$result[0]['title'];
									echo $title;
									break;
								}?></td>
                               
                                <td align="center">
                                <?php
								$sql2="select SMS from customer_reminder where id='".$res[$i]['rem_id']."'";
								$result2=$this->m_dbConnRoot->select($sql2);
								$sms=$result2[0]['SMS'];
								if($sms==0)
								{
									if($res[$i]['ReminderType']==1)
									{
										echo "Yes";
									}
									else
									{
										echo "NO";
									}
								}
								else
								{
									echo "YES";
								}
								
								 ?>
                                </td>
                                <td align="center">
                                <?php
								$sql3="select EMAIL from customer_reminder where id='".$res[$i]['rem_id']."'";
								$result3=$this->m_dbConnRoot->select($sql3);
								$email=$result3[0]['EMAIL'];
								if($email==0)
								{
									if($res[$i]['ReminderType']==2||$res[$i]['ReminderType']==3)
									{
										echo "Yes";
									}
									else
									{
										echo "NO";
									}
								}
								else
								{
									echo "YES";
								}
								 ?>
                                </td>
                                <td align="center">
                                 <?php
								$sql4="select MOBILE_NOTIFY from customer_reminder where id='".$res[$i]['rem_id']."'";
								$result4=$this->m_dbConnRoot->select($sql4);
								$mobile=$result4[0]['MOBILE_NOTIFY'];
								if($mobile==0)
								{
									echo "NO";
								}
								else
								{
									echo "YES";
								}
								 ?>
                                </td>

                               <?php if($res[$i]['rem_type']==1){ ?>
                            <td align="center"><a href="customer_reminder.php?method=edit&Id=<?php echo $res[$i]['ID']; ?>"><img src="images/edit.gif"  /></a></td> <?php } else{ ?>
                            		 <td align="center"></td>
                            <?php }?>
                            <td align="center"><a onClick="deleteCustRemType(<?php echo $res[$i]['ID']; ?>)"><img src="images/del.gif" /></a></td>
                   <?php }?>
                   </tr>
                   <?php }?>
                   
            </tbody>
        <?php } 
		else
		{ ?>
        		<thead>
            	<tr  height="30" bgcolor="#FFFFFF">
                	<?php if($this->obj_utility->checkAccess()=="0")
							{
					?>
            						<th style="text-align:center">Edit</th>
            						<th style="text-align:center">Delete</th>
                    <?php
							}
					?>
                	  <th style="text-align:center">Sr.No</th>
           			  <th style="text-align:center">Date</th>  
            		  <th style="text-align:center">Reminder Type</th>
                      <th style="text-align:center">Title</th>                  
            	</tr>
            </thead>
            <tbody>
            	<tr>
                <?php if($this->obj_utility->checkAccess()=="0")
				{
				?>	
                    <td></td>
                    <td></td>
                    <td></td>
            		<td style="text-align:center"><font color="#FF0000" size="2"><b>No Records Found.</b></font></td>
                    <td></td>
                    <td></td>
                    <td></td>
                 <?php
				}
				else
				{
				?>
                    <td></td>
                    <td></td>
            		<td style="text-align:center"><font color="#FF0000" size="2"><b>No Records Found.</b></font></td>
                    <td></td>
                    <td></td>
                 <?php
				}
				?>
            	</tr>
            </tbody>
        
   <?php } ?>
        </table>
	<?php }
	
	public function displaypsms($res)
	{?>
		<table id="example" class="display" cellspacing="0">
        <?php if($res<>"")
		{?>
        	<thead>
            <tr height="30" bgcolor="#FFFFFF">
            <th style="text-align:center">Sr.No</th>
            <th style="text-align:center">Event Date</th> 
            <th style="text-align:center">Event Reminder Date</th>  
            <th style="text-align:center">Reminder Category</th>
            <th style="text-align:center">Reminder</th> 
            <th style="text-align:center">SMS</th>
            <th style="text-align:center">EMAIL</th> 
            <th style="text-align:center">MOBILE_NOTIFY</th> 
              <?php if($this->obj_utility->checkAccess()=="0")
					{
					?>
                	   <th style="text-align:center">Edit</th>
            		   <th style="text-align:center">Delete</th> 
                    <?php
					}
					?>         
            </tr>
            </thead>
            <tbody>
             <?php $k=1;
			 for($i=0;$i<sizeof($res);$i++){ ?>
            	<tr height="25" bgcolor="#BDD8F4" align="center"> 
                    	<?php
						if($this->obj_utility->checkAccess()=="0")
						{
						?>
                        	<td align="center"><?php echo $k++;?></td>
                        	<td align="center"><?php echo getDisplayFormatDate($res[$i]['EventDate']);?></td>
                            <td align="center"><?php if(getDisplayFormatDate($res[$i]['rem_before'])<>''){ ?>
								<?php echo getDisplayFormatDate($res[$i]['rem_before']);?></td>
                                <?php }else{
									echo getDisplayFormatDate($res[$i]['EventDate']);
								}
									?>
                                

							<td align="center"><?php if($res[$i]['rem_type']==0)
														{ 
															echo 'System Generated';
														}
														else
														{
															echo "User Defined";
														}
														?></td>
                            <td align="center"><?php switch ($res[$i]['ReminderType']){
								case 1:
									echo "Bill Reminder";
									break;
								case 2:
									echo "Event Reminder";
									break;
								case 3:
									echo "FD Maturity Reminder";
									break;
								default:
									$status="Y";
									$sql="select title from customer_reminder where id='".$res[$i]['rem_id']."'";
									$result=$this->m_dbConnRoot->select($sql);
									$title=$result[0]['title'];
									echo $title;
									break;
								}?></td>
                               
                                <td align="center">
                                <?php
								$sql2="select SMS from customer_reminder where id='".$res[$i]['rem_id']."'";
								$result2=$this->m_dbConnRoot->select($sql2);
								$sms=$result2[0]['SMS'];
								if($sms==0)
								{
									if($res[$i]['ReminderType']==1)
									{
										echo "Yes";
									}
									else
									{
										echo "NO";
									}
								}
								else
								{
									echo "YES";
								}
								
								 ?>
                                </td>
                                <td align="center">
                                <?php
								$sql3="select EMAIL from customer_reminder where id='".$res[$i]['rem_id']."'";
								$result3=$this->m_dbConnRoot->select($sql3);
								$email=$result3[0]['EMAIL'];
								if($email==0)
								{
									if($res[$i]['ReminderType']==2||$res[$i]['ReminderType']==3)
									{
										echo "Yes";
									}
									else
									{
										echo "NO";
									}
								}
								else
								{
									echo "YES";
								}
								 ?>
                                </td>
                                <td align="center">
                                 <?php
								$sql4="select MOBILE_NOTIFY from customer_reminder where id='".$res[$i]['rem_id']."'";
								$result4=$this->m_dbConnRoot->select($sql4);
								$mobile=$result4[0]['MOBILE_NOTIFY'];
								if($mobile==0)
								{
									echo "NO";
								}
								else
								{
									echo "YES";
								}
								 ?>
                                </td>

                              
                            		 <td align="center"></td>
                          
                              <td align="center"><a onClick="deleteProcessCustRemType(<?php echo $res[$i]['ID']; ?>)"><img src="images/del.gif" /></a></td>
                   <?php }?>
                   </tr>
                   <?php }?>
                   
            </tbody>
        <?php } 
		else
		{ ?>
        		<thead>
            	<tr  height="30" bgcolor="#FFFFFF">
                	<?php if($this->obj_utility->checkAccess()=="0")
							{
					?>
            						<th style="text-align:center">Edit</th>
            						<th style="text-align:center">Delete</th>
                    <?php
							}
					?>
                	  <th style="text-align:center">Sr.No</th>
           			  <th style="text-align:center">Date</th>  
            		  <th style="text-align:center">Reminder Type</th>
                      <th style="text-align:center">Title</th>                  
            	</tr>
            </thead>
            <tbody>
            	<tr>
                <?php if($this->obj_utility->checkAccess()=="0")
				{
				?>	
                    <td></td>
                    <td></td>
                    <td></td>
            		<td style="text-align:center"><font color="#FF0000" size="2"><b>No Records Found.</b></font></td>
                    <td></td>
                    <td></td>
                    <td></td>
                 <?php
				}
				else
				{
				?>
                    <td></td>
                    <td></td>
            		<td style="text-align:center"><font color="#FF0000" size="2"><b>No Records Found.</b></font></td>
                    <td></td>
                    <td></td>
                 <?php
				}
				?>
            	</tr>
            </tbody>
        
   <?php } ?>
        </table>
	<?php }
	
	public function getSMSdata()
	{
		$status="Y";
		$society_id=$_SESSION['society_id'];
		$sql="select * from remindersms where CronJobTimestamp='0000-00-00 00:00:00' and society_id='".$society_id."' and rem_status='".$status."'";
		$result=$this->m_dbConnRoot->select($sql);
		//var_dump($result);
		$data=$this->displaysms($result);
		return $data;
		//return $result;
		
	}
	public function getSMSProdata()
	{
		$status="Y";
		$society_id=$_SESSION['society_id'];
		$sql="select * from remindersms where CronJobTimestamp<>'0000-00-00 00:00:00' and society_id='".$society_id."' and rem_status='".$status."'";
		$result=$this->m_dbConnRoot->select($sql);
		//var_dump($result);
		$data=$this->displaypsms($result);
		return $data;
		//return $result;
		
	}
	public function getAllRemdata($id)
	{
		$sql1="select rem_id from remindersms where ID='".$id."'";
		$res1=$this->m_dbConnRoot->select($sql1);
		$rem_id=$res1[0]['rem_id'];
		
		$sql2="select * from customer_reminder where id='".$rem_id."'";
		$res2=$this->m_dbConnRoot->select($sql2);
		return $res2;
		
	}
	public function deleteRemdata($id)
	{
		$sql1="select rem_id from remindersms where ID='".$id."'";
		$res1=$this->m_dbConnRoot->select($sql1);
		$rem_id=$res1[0]['rem_id'];
		
		$status="N";
		$sql2="update customer_reminder set status='".$status."' where id='".$rem_id."'";
		$res2=$this->m_dbConnRoot->update($sql2);
		$sql3="update remindersms set rem_status='".$status."' where ID='".$id."'";
		$res3=$this->m_dbConnRoot->update($sql3);
		
		return $res3;
		
		
	}
	public function deleteProcessRemdata($id)
	{
		$status="N";
		$sql3="update remindersms set rem_status='".$status."' where ID='".$id."'";
		$res3=$this->m_dbConnRoot->update($sql3);
		
		return $res3;
	}
	
	
}

?>
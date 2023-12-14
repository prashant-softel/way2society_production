<?php if(!isset($_SESSION)){ session_start(); }
//include_once("include/dbop.class.php");
include_once("include/display_table.class.php");
include_once("dbconst.class.php");
include_once("utility.class.php");
include_once("activate_user_email.class.php");

class list_member extends dbop
{
	public $actionPage = "../list_member.php";
	public $m_dbConn;
	public $obj_utility;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$dbopRoot = new dbop(true);
		$this->obj_utility = new utility($this->m_dbConn, $dbopRoot);
		$this->display_pg = new display_table($this->m_dbConn);
		$this->obj_activation = new activation_email($this->m_dbConn, $dbopRoot);
		//dbop::__construct();
	}
	
	public function combobox($query)
	{
	$str.="<option value=''>Please Select</option>";
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
						$str.="<OPTION VALUE=".$v.">";
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
	public function combobox07($query,$id)
	{
	$str.="<option value=''>All</option>";
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
	public function display1($rsas)
	{
			$thheader=array('Member Name','Parking Slot','Bike Reg No.','Bike Owner','Bike Model','Bike Make','Bike Color');
			$this->display_pg->edit="list_member";
			$this->display_pg->th=$thheader;
			$this->display_pg->mainpg="list_member.php";
			
			//$res = $this->display_pg->display_new($rsas);
			$res = $this->list_member_show($rsas);
			
			return $res;
	}
	public function pgnation()
	{

		$arUnitsWithAppInstalled = $this->obj_utility->GetListMobileAppUsers();
		$arAppStatus = array();
		foreach ($arUnitsWithAppInstalled as $key => $value) 
		{
			//print_r($value);
			$UserUnitID = $value["unit_id"];
			array_push($arAppStatus, $UserUnitID);
		}
		//print_r($arAppStatus);
		//print_r($arUnitsWithAppInstalled);
		$sql1 = "SELECT * FROM society as s,member_main as mm,unit as u,wing as w where mm.unit=u.unit_id and u.wing_id=w.wing_id and mm.society_id=s.society_id and mm.status='Y' and u.status='Y' and w.status='Y' and mm.ownership_status='1' ";

		//echo $sql1;
		if(isset($_SESSION['admin']) || isset($_SESSION['sadmin']))
		{
			$sql1 .= " and s.society_id = '".$_SESSION['society_id']."'";
		}
		
		if($_REQUEST['society_id']<>"")
		{
			$sql1 .= " and s.society_id = '".$_REQUEST['society_id']."'";
		}
		if($_REQUEST['wing_id']<>"")
		{
			$sql1 .= " and w.wing_id = '".$_REQUEST['wing_id']."'";
		}
		
		if($_REQUEST['unit_no'] <>"")
		{
			$sql1 .= " and u.unit_no = '".$_REQUEST['unit_no']."'";
		}
		if($_REQUEST['member_name']<>"")
		{
			$sql1 .= " and mm.owner_name like '%".addslashes($_REQUEST['member_name'])."%'";
		}
		
		if($_REQUEST['mob_no'] <>"")
		{
			$sql1 .= " and mm.mob like '%".addslashes($_REQUEST['mob_no'])."%'";
		}
		
		if($_REQUEST['email_id'] <>"")
		{
			$sql1 .= " and mm.email like '%".addslashes($_REQUEST['email_id'])."%'";
		}
		
		
		$sql1 .= " order by wing,u.sort_order";
		
		$result = $this->m_dbConn->select($sql1);
		
		
		
		for($i=0;$i<sizeof($result);$i++)
		{
			//$sql2="Select * from `tenant_module` where unit_id='".$result[$i]['unit_id']."' and active=1";
			 $sql2="SELECT *,mm.email as 'Tenant_Email',tm.active FROM tenant_member mm,tenant_module tm where curdate() < tm.end_date and mm.tenant_id=tm.tenant_id && tm.unit_id='".$result[$i]['unit_id']."'";
			$result1 = $this->m_dbConn->select($sql2);
			//echo "<pre>";
			//print_r($result1 );
			$size = sizeof($result1) -1; 
			//echo "</pre>";
			//foreach($result as $res)
			$date = $this->obj_utility->getDateDiff($result1[$i]["end_date"], date("Y-m-d"));
			{
				if($date >= 0)
				{ $result[$i]['Tenant_id']=$result1[$size]['tenant_id'];
					if($result1[$size]['active'] == 1)
					{
					$result[$i]['Tenant_name']=$result1[$size]['tenant_name'];
					}
				else if($result1[$size]['tenant_name'] <> '')
				{
					if($_SESSION['role'] == ROLE_SUPER_ADMIN || $_SESSION['role'] == ROLE_ADMIN || $_SESSION['role']==ROLE_ACCOUNTANT || $_SESSION['role'] == ROLE_MANAGER || ($_SESSION['role'] == ROLE_ADMIN_MEMBER && $_SESSION['profile'][PROFILE_EDIT_MEMBER] == 1)){	
				 	
				 	$result[$i]['Tenant_name']=  $result1[$size]['tenant_name']."<br><a href='tenant.php?mem_id=".$result[$i]['member_id']."&tik_id=".time()."&edit=".$result[$i]['Tenant_id']."'><span style='color:red;font-waight:bold'> Waiting For Approval.</span></a>";
					}
					else{
						$result[$i]['Tenant_name']=  $result1[$size]['tenant_name']."<br><span style='color:red;font-waight:bold'> Waiting For Approval.</span>";
					}
                   
				 //}
				}
				}
			$result[$i]['Tenant_Email']=$result1[$size]['Tenant_Email'];
			$result[$i]['Tenant_contact']=$result1[$size]['contact_no'];
			$result[$i]['Tenant_StartDate']=$result1[$size]['start_date'];
			$result[$i]['Tenant_EndDate']=$result1[$size]['end_date'];
			}
			
			
			//$sql3="select * from mem_bike_parking where member_id='".$result[$i]['member_id']."' ";
			$sql3=	"SELECT *, GROUP_CONCAT(bike_reg_no),GROUP_CONCAT(parking_slot),GROUP_CONCAT(bike_model) FROM mem_bike_parking where member_id='".$result[$i]['member_id']."' AND status='Y' GROUP BY member_id";
			
			$result2 = $this->m_dbConn->select($sql3);
			/*echo "<pre>";
			print_r($result2);
			echo "</pre>";*/
			{
			$result[$i]['Bike_reg']=$result2[0]['GROUP_CONCAT(bike_reg_no)'];
			$result[$i]['Bike_parking']=$result2[0]['GROUP_CONCAT(parking_slot)'];
			$result[$i]['Bike_model']=$result2[0]['GROUP_CONCAT(bike_model)'];
			}
			
			$sql4="SELECT *, GROUP_CONCAT(car_reg_no),GROUP_CONCAT(parking_slot),GROUP_CONCAT(car_model) FROM mem_car_parking where member_id='".$result[$i]['member_id']."' AND status='Y' GROUP BY member_id";
			$result3 = $this->m_dbConn->select($sql4);
			/*echo "<pre>";
			print_r($result3);
			echo "</pre>";*/
			{
			$result[$i]['Car_reg']=$result3[0]['GROUP_CONCAT(car_reg_no)'];
			$result[$i]['Car_parking']=$result3[0]['GROUP_CONCAT(parking_slot)'];
			$result[$i]['Car_model']=$result3[0]['GROUP_CONCAT(car_model)'];
			}
			
			
			$sql5="select * from unit where unit_id='".$result[$i]['unit_id']."' ";
			$result4 = $this->m_dbConn->select($sql5);
			{
			if($result4[0]['block_unit'] == 0)
			{

				$reason='<font color="#FF0000"><b></b></font>';
				$block= 'NO';
			}
			else if($result4[0]['block_unit'] == 1)
			{	
				$reason='<font color="#FF0000"><b>'.$result4[0]['block_desc'].'</b></font>';
				$block= '<font color="#FF0000"><b>YES</b></font>';
			}
			$result[$i]['Blocked']=$block;
			$result[$i]['reason']=$reason;
			$result[$i]['Intercom_no']=$result4[0]['intercom_no'];
			$result[$i]['Nominee']=$result4[0]['nominee_name'];
			$result[$i]['Share_Certificate']=$result4[0]['share_certificate'];	
			$result[$i]['taxable_no_threshold'] = $result4[0]['taxable_no_threshold'];	
			$ssppStatus = "";
			
			if(in_array($result4[0]['unit_id'],$arAppStatus))
			{
				//echo "matched:";
				//$sAppStatus = '<i class="fa fa-mobile-phone" style="font-size:10px;font-size:1.75vw;color:#6698FF;"></i>';
				$sAppStatus = '<img  src="images/android.svg" style="height:30px;width:30px"/>';
     	 	}
     	 	else
     	 	{
     	 		$sAppStatus = "";
     	 	}
			

			$result[$i]['AppStatus']=$sAppStatus;

			}
			
			$sql6="SELECT *,count(mem_other_family_id) as Member_Count FROM `mem_other_family`where member_id='".$result[$i]['member_id']."' and NOT relation='Self'";
			$result5 = $this->m_dbConn->select($sql6);
			{
			$result[$i]['Additional_Member_Count']=$result5[0]['Member_Count'];	
			}
			
			// Added on get Login Status 20190831 
			 $RetStatus = $this->obj_activation->CheckIfMappingAlreadyExist($result[$i]['email'],$_SESSION['society_id'], $result[$i]['unit_id']);
			 if($RetStatus == ACCOUNT_EXIST_ACTIVE)
				{
					$mLogin= "Active";
				}
				else
				{
					$mLogin =  "Inactive";
				}
				$result[$i]['LoginStatus']=$mLogin;
		}
		//echo "<pre>";
		//print_r($result);
		//echo "</pre>";
		$this->list_member_show($result);
	
	}
	
	public function SundryDebtorsList(){
		
		$SundryDebtorsQuert = "SELECT s.UnitID FROM `sale_invoice` as s JOIN ledger as l ON s.UnitID = l.id Where l.categoryid != 4";
		$Result = $this->m_dbConn->select($SundryDebtorsQuert);
		return $Result = array_unique(array_column($Result,'UnitID'));
		}
	
	
	
	public function getAllUnits()
	{
		$sql="select `unit_id` from `unit` where `society_id` = ".$_SESSION['society_id']." and `status` = 'Y' order by sort_order asc";
		$res=$this->m_dbConn->select($sql);
		$flatten = array();
    	foreach($res as $key)
		{
			$flatten[] = $key['unit_id'];
		}

    	return $flatten;
	}
	public function list_member_show($res)
	{
		if($res<>"")
		{
			if(!isset($_REQUEST['page']))
			{
				$_REQUEST['page'] = 1;
			}
			$iCounter = 1 + (($_REQUEST['page'] - 1) * 50);
			$UnitArray = $this->getAllUnits();
			
			$EncodeUnitArray;
			$EncodeUrl;
			if(sizeof($UnitArray) > 0)
			{
				$EncodeUnitArray = json_encode($UnitArray);
				$EncodeUrl = urlencode($EncodeUnitArray);
			}
			
		?>
        <table id="example" class="display" cellspacing="0" style="width:100%">
		<thead>
        <tr>
        	<th width="50">Sr No.</th>
        	<!--<th width="200">Society Name</th>-->
        	<th width="70">Wing</th>
            <th width="60">Unit No.</th>
            <th width="60">Area</th>
        	<th width="250">Members Name</th>
            <th width="20">Dues</th>
            <th width="80">Mobile No.</th>
            <th width="150">Members Email</th>
            <th width="50">Reverse charge/Credit</th>
            <th width="50">No Threshold for GST</th>
            <th width="50">Intercom Number</th>
            <th width="50">Bike Registration No.</th>
            <th width="50">Bike Parking Slot No.</th> 
            <th width="50">Bike Model Name</th>  
            <th width="50">Car Registration No.</th>
            <th width="50">Car Parking Slot No.</th> 
            <th width="50">Car Model Name</th>       
            <th width="50">Tenant Name</th> 
            <th width="50">Tenant Email</th> 
            <th width="50">Tenant Contact No</th>
            <th width="50">Tenant Start Date</th>
            <th width="50">Tenant End Date</th>
            <!--<th width="50">Intercom Number</th>-->
            <th width="50">Nominee Name</th>
            <th width="50">Share Certificate No.</th>
            <th width="50">Additional Member Count</th> 
            <th width="50">Is unit suspended?</th>
            <th width="50">Reason for suspend</th>
	    <th width="50">Is App Installed?</th>
        <th width="50">Login Status</th>

         <!--   <th width="50">Reverse charge/Credit</th>-->
                     
            
			<?php if($_SESSION['role'] == ROLE_SUPER_ADMIN || $_SESSION['role'] == ROLE_ADMIN)
            {?>
            	<!--<th width="50">View</th>-->
			<?php }?>
            <?php if($_SESSION['role'] <> ROLE_ADMIN_MEMBER)
			{?>
             <!--<th width="50">Transfer Ownership</th>-->
            <?php }?>
			<?php if(IsReadonlyPage() == false && ($_SESSION['role'] == ROLE_SUPER_ADMIN || $_SESSION['role'] == ROLE_ADMIN ||$_SESSION['role'] == ROLE_MANAGER || $_SESSION['role']==ROLE_ACCOUNTANT )){?>
            <th width="50">Edit</th>
           <!-- <th width="70">Delete</th>-->
            <?php } ?>
        </tr>
		</thead>
		<tbody>
        <?php 
		foreach($res as $k => $v)
		{
			if(sizeof($UnitArray) > 0)
			{
				$Url = "member_ledger_report.php?&uid=".$res[$k]['unit_id']."&Cluster=".$EncodeUrl;
			}
			else
			{
				$Url = "member_ledger_report.php?&uid=".$res[$k]['unit_id'];
			}?>
        <tr height="25" bgcolor="#BDD8F4" align="center">
        	<td align="center"><?php echo $iCounter++;?></td>
        	<!--<td align="center"><?php //echo $res[$k]['society_name']?></td>-->
        	<td align="center"><?php echo $res[$k]['wing'];?></td>
            <td align="center">
			<?php if($_SESSION['role'] == ROLE_SUPER_ADMIN || $_SESSION['role'] == ROLE_ADMIN || $_SESSION['role']==ROLE_ACCOUNTANT || $_SESSION['role'] == ROLE_MANAGER || ($_SESSION['role'] == ROLE_ADMIN_MEMBER && $_SESSION['profile'][PROFILE_EDIT_MEMBER] == 1))
            {?>
            <a href="view_member_profile.php?scm&id=<?php echo $res[$k]['member_id'];?>&tik_id=<?php echo time();?>&m&view" target="_blank"><?php echo $res[$k]['unit_no']?></a>
            <?php 
			}
			else
			{
				echo $res[$k]['unit_no'];
			}?>
            </td>
            <td align="center"><?php echo $res[$k]['area'];?></td>
        	<td align="center">
			<?php if($_SESSION['role'] == ROLE_SUPER_ADMIN || $_SESSION['role'] == ROLE_ADMIN || $_SESSION['role']==ROLE_ACCOUNTANT || $_SESSION['role'] == ROLE_MANAGER  || ($_SESSION['role'] == ROLE_ADMIN_MEMBER && $_SESSION['profile'][PROFILE_EDIT_MEMBER] == 1))
            {?>
            <a href="view_member_profile.php?scm&id=<?php echo $res[$k]['member_id'];?>&tik_id=<?php echo time();?>&m&view" target="_blank"><?php echo $res[$k]['owner_name']?></a>
            <?php 
			}
			else
			{
				echo $res[$k]['owner_name'];
			}?>
            </td>
            <td align="center"><a href="#" onClick="window.open('<?php echo $Url; ?>','popup','type=fullWindow,fullscreen,scrollbars=yes');" style="color:#0000FF;"><?php echo $this->obj_utility->getDueAmount($res[$k]['unit_id']);;?></a></td>
            <td align="center"><?php echo $res[$k]['mob'];?></td>
            <td align="center"><a href="mailto:<?php echo $res[$k]['email'];?>" style="color:#0000FF" target="_blank"><?php echo $res[$k]['email'];?></a></td>
            <!--<td align="center"><a href="mailto:<?php echo $res[$k]['email'];?>" style="color:#0000FF" target="_blank"><?php echo $res[$k]['email'];?></a></td>-->
            
            
            
            
            <td align="center">
            <a href="reverse_charges.php?&uid=<?php echo $res[$k]['unit_id'];?>" style="color:#0000FF;" target="_blank"><b>Reverse charge/Credit</b></a>
            </td>
             </td>
            <td align="center">
         	 <?php if($res[$k]['taxable_no_threshold'] == 1){ echo 'YES';}else{ echo 'NO'; };?>
            </td>
            <td align="center">
         	 <?php echo $res[$k]['Intercom_no'];?>
            </td>
             <td align="center">
             <?php echo $res[$k]['Bike_reg'];?>
             </td>
             
            <td align="center">
            <?php echo $res[$k]['Bike_parking'];?>
            </td>
            
            <td align="center">
            <?php echo $res[$k]['Bike_model'];?>
            </td>

            <td align="center">
             <?php echo $res[$k]['Car_reg'];?>
            </td>
            
             <td align="center">
              <?php echo $res[$k]['Car_parking'];?>
             </td>
            <td align="center">
            <?php echo $res[$k]['Car_model'];?> 
            </td>
            
            <td align="center">
          <?php echo $res[$k]['Tenant_name'];?>
            </td>
            
            <td align="center">
            	<a href="mailto:<?php echo $res[$k]['Tenant_Email'];?>" style="color:#0000FF" target="_blank"><?php echo $res[$k]['Tenant_Email'];?></a>
            </td>
            
            <td align="center">
          <?php echo $res[$k]['Tenant_contact'];?>
            </td>
            
            <td align="center">
          	<?php echo $res[$k]['Tenant_StartDate'];?>
            </td>
            
            <td align="center">
          	<?php echo $res[$k]['Tenant_EndDate'];?>
            </td>
 
 			<!--<td align="center">
         	 <?php //echo $res[$k]['Intercom_no'];?>
            </td>
            -->
            <td align="center">
         	 <?php echo $res[$k]['Nominee'];?>
            </td>
            
            <td align="center">
         	 <?php echo $res[$k]['Share_Certificate'];?>
            </td>
            
           	<td align="center">
         	 <?php echo $res[$k]['Additional_Member_Count'];?>
            </td>
            
            <td align="center">
         	 <?php echo $res[$k]['Blocked'];?>
            </td>
	    <td align="center">
         	 <?php echo $res[$k]['reason'];?>
            </td>
            <td align="center">
         	 <?php 
         	 	echo $res[$k]['AppStatus'];
         	 	?>
            </td>
            <td align="center">
         	 <?php 
         	 	echo $res[$k]['LoginStatus'];
         	 	?>
            </td>
           <!--  <td align="center">
            <a href="reverse_charges.php?&uid=<?php //echo $res[$k]['unit_id'];?>" style="color:#0000FF;"><b>Reverse charge/Credit</b></a>
            </td>-->
            
            <?php if($_SESSION['role'] <> ROLE_ADMIN_MEMBER)
			{?>
            <!--<td align="center">  <a href="unit.php?mtfr&uid=<?php //echo $res[$k]['unit_id'];?>"><img src="images/transfer.png"  style="width: 18px;"/></a></td>-->
            <?php }?>
            <?php if(IsReadonlyPage() == false && ($_SESSION['role'] == ROLE_SUPER_ADMIN || $_SESSION['role'] == ROLE_ADMIN || $_SESSION['role']==ROLE_ACCOUNTANT || $_SESSION['role'] == ROLE_MANAGER )){?>
            <td align="center">
            <a href="view_member_profile.php?edt&scm&id=<?php echo $res[$k]['member_id'];?>&tik_id=<?php echo time();?>&m&view" target="_blank">
            <img src="images/edit.gif" />
            </a>
            </td>
            
            <!--<td align="center">
            <?php //if($this->chk_delete_perm_admin()==1){?>
            <a href="javascript:void(0);" onclick="del_member(<?php //echo $res[$k]['member_id']?>);"><img src="images/del.gif" /></a>
            <?php// }else{?>
            <a href="del_control_admin.php?prm" target="_blank" style="text-decoration:none;"><font color=#FF0000 style='font-size:10px;'><b>Not Allowed</b></font></a>
            <?php //}?>
            </td>-->
            <?php 
			} 
			?>
        </tr>
        <?php }?>
		</tbody>
        </table>
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
		}
	}
	public function chk_delete_perm_admin()
	{
		$sql = "select * from del_control_admin where status='Y' and login_id='".$_SESSION['login_id']."'";
		$res = $this->m_dbConn->select($sql);
		return $res[0]['del_control_admin'];
	}
	public function selecting()
	{
		$sql1="select mem_bike_parking_id,`member_id`,`parking_slot`,`bike_reg_no`,`bike_owner`,`bike_model`,`bike_make`,`bike_color` from mem_bike_parking where mem_bike_parking_id='".$_REQUEST['mem_bike_parkingId']."'";
		$var=$this->m_dbConn->select($sql1);
		return $var;
	}
	public function del_member()
	{
		$kk = 2;
		$pp = 0;
		
		if($kk==1)
		{
			$sql1 = "update member_main set status='N' where member_id='".$_REQUEST['member_id']."' and status='Y'";
			$sql2 = "update mem_spouse_details set status='N' where member_id='".$_REQUEST['member_id']."' and status='Y'";
			$sql3 = "update mem_child_details set status='N' where member_id='".$_REQUEST['member_id']."' and status='Y'";
			$sql4 = "update mem_other_family set status='N' where member_id='".$_REQUEST['member_id']."' and status='Y'";
			$sql5 = "update mem_bike_parking set status='N' where member_id='".$_REQUEST['member_id']."' and status='Y'";
			$sql6 = "update mem_car_parking set status='N' where member_id='".$_REQUEST['member_id']."' and status='Y'";
			$sql7 = "update login set status='N' where com_id='".$_REQUEST['member_id']."' and status='Y'";
			
			if($pp==1)
			{
			$res1 = $this->m_dbConn->update($sql1);
			$res2 = $this->m_dbConn->update($sql2);
			$res3 = $this->m_dbConn->update($sql3);
			$res4 = $this->m_dbConn->update($sql4);
			$res5 = $this->m_dbConn->update($sql5);
			$res6 = $this->m_dbConn->update($sql6);
			$res7 = $this->m_dbConn->update($sql7);
			}
		}
		else
		{
			$sql1 = "delete from member_main where member_id='".$_REQUEST['member_id']."' and status='Y'";
			$sql2 = "delete from mem_spouse_details where member_id='".$_REQUEST['member_id']."' and status='Y'";
			$sql3 = "delete from mem_child_details where member_id='".$_REQUEST['member_id']."' and status='Y'";
			$sql4 = "delete from mem_other_family where member_id='".$_REQUEST['member_id']."' and status='Y'";
			$sql5 = "delete from mem_bike_parking where member_id='".$_REQUEST['member_id']."' and status='Y'";
			$sql6 = "delete from mem_car_parking where member_id='".$_REQUEST['member_id']."' and status='Y'";
			$sql7 = "delete from login where com_id='".$_REQUEST['member_id']."' and status='Y'";
			
			
			$res1 = $this->m_dbConn->delete($sql1);
			$res2 = $this->m_dbConn->delete($sql2);
			$res3 = $this->m_dbConn->delete($sql3);
			$res4 = $this->m_dbConn->delete($sql4);
			$res5 = $this->m_dbConn->delete($sql5);
			$res6 = $this->m_dbConn->delete($sql6);
			$res7 = $this->m_dbConn->delete($sql7);
		}
	}
	
	public function soc_name($society_id)
	{
		$sql = "select * from society where society_id='".$society_id."' and status='Y'";
		$res = $this->m_dbConn->select($sql);
		echo $res[0]['society_name'];
	}
	
	public function get_wing()
	{
		if($_REQUEST['society_id']<>"")
		{	
			$sql = "select * from wing where status='Y' and society_id='".$_REQUEST['society_id']."'";
		}
		else
		{
			$sql = "select * from wing where status='Y'";	
		}
		$res = $this->m_dbConn->select($sql);	
			
		if($res<>"")
		{
			$i=0;
			foreach($res as $k => $v)
			{
			 echo $res[$k]['wing_id']."#".$res[$k]['wing']."###";
			 $i++;
			}
			echo "****".$i;
		}
		else
		{
			echo ""."#"."0";
			echo "****"."0";
		}
	}
	
	public function get_wing_new()
	{
		if($_REQUEST['society_id']<>"")
		{	
			$sql = "select * from wing where status='Y' and society_id='".$_REQUEST['society_id']."'";
		}
		else
		{
			$sql = "select * from wing where status='Y'";	
		}
		$res = $this->m_dbConn->select($sql);	
			
		if($res<>"")
		{
			$aryResult = array();
			foreach($res as $k => $v)
			{
			 	$show_dtl = array("id"=>$res[$k]['wing_id'], "wing"=>$res[$k]['wing']);
				array_push($aryResult,$show_dtl);
			}
			echo json_encode($aryResult);
		}
		else
		{
			echo json_encode(array(array("success"=>1), array("message"=>'No Data To Display')));
		}
	}
	
	public function get_society_new()
	{
		$sql = "select * from society where status='Y'";	
		
		$res = $this->m_dbConn->select($sql);	
			
		if($res<>"")
		{
			$aryResult = array();
			foreach($res as $k => $v)
			{
			 	$show_dtl = array("id"=>$res[$k]['society_id'], "society"=>$res[$k]['society_name']);
				array_push($aryResult,$show_dtl);
			}
			echo json_encode($aryResult);
		}
		else
		{
			echo json_encode(array(array("success"=>1), array("message"=>'No Data To Display')));
		}
	}
	
	
	public function display_society_name($society_id)
	{
		$sql="select society_name from society where society_id=".$society_id." ";
		$data=$this->m_dbConn->select($sql);
		return $data[0]['society_name'];
	}
	
	/* Added New Funtion get member data for list_member2.php page */
	public function pgnationNew()
	{
		
	
		$sql1 = "SELECT * FROM society as s,member_main as mm,unit as u,wing as w where mm.unit=u.unit_id and u.wing_id=w.wing_id and mm.society_id=s.society_id and mm.status='Y' and u.status='Y' and w.status='Y' and mm.ownership_status='1' ";

		//echo $sql1;
		if(isset($_SESSION['admin']) || isset($_SESSION['sadmin']))
		{
			$sql1 .= " and s.society_id = '".$_SESSION['society_id']."'";
		}
		
		if($_REQUEST['society_id']<>"")
		{
			$sql1 .= " and s.society_id = '".$_REQUEST['society_id']."'";
		}
		if($_REQUEST['wing_id']<>"")
		{
			$sql1 .= " and w.wing_id = '".$_REQUEST['wing_id']."'";
		}
		
		if($_REQUEST['unit_no'] <>"")
		{
			$sql1 .= " and u.unit_no = '".$_REQUEST['unit_no']."'";
		}
		if($_REQUEST['member_name']<>"")
		{
			$sql1 .= " and mm.owner_name like '%".addslashes($_REQUEST['member_name'])."%'";
		}
		
		if($_REQUEST['mob_no'] <>"")
		{
			$sql1 .= " and mm.mob like '%".addslashes($_REQUEST['mob_no'])."%'";
		}
		
		if($_REQUEST['email_id'] <>"")
		{
			$sql1 .= " and mm.email like '%".addslashes($_REQUEST['email_id'])."%'";
		}
		
		
		$sql1 .= " order by wing,u.sort_order";
		
		$result = $this->m_dbConn->select($sql1);
		
		$this->list_member_showNew($result);
	}
	public function list_member_showNew($res)
	{
		if($res<>"")
		{
			if(!isset($_REQUEST['page']))
			{
				$_REQUEST['page'] = 1;
			}
			$iCounter = 1 + (($_REQUEST['page'] - 1) * 50);
			$UnitArray = $this->getAllUnits();
			
			$EncodeUnitArray;
			$EncodeUrl;
			if(sizeof($UnitArray) > 0)
			{
				$EncodeUnitArray = json_encode($UnitArray);
				$EncodeUrl = urlencode($EncodeUnitArray);
			}
			
		?>
        <table id="example" class="display" cellspacing="0" style="width:100%">
		<thead>
        <tr>
        	<th width="50">Sr No.</th>
        	<th width="70">Wing</th>
            <th width="60">Unit No.</th>
            <th width="60">Area</th>
			<th width="50">Flat Configuration</th>
        	<th width="250">Members Name</th>
            <th width="20">Dues</th>
            <th width="80">Mobile No.</th>
            <th width="150">Members Email</th>
			<?php if(IsReadonlyPage() == false && ($_SESSION['role'] == ROLE_SUPER_ADMIN || $_SESSION['role'] == ROLE_ADMIN ||$_SESSION['role'] == ROLE_MANAGER || $_SESSION['role']==ROLE_ACCOUNTANT )){?>
            <th width="50">Edit</th>
           
            <?php } ?>
        </tr>
		</thead>
		<tbody>
        <?php 
		foreach($res as $k => $v)
		{
			if(sizeof($UnitArray) > 0)
			{
				$Url = "member_ledger_report.php?&uid=".$res[$k]['unit_id']."&Cluster=".$EncodeUrl;
			}
			else
			{
				$Url = "member_ledger_report.php?&uid=".$res[$k]['unit_id'];
			}?>
        <tr height="25" bgcolor="#BDD8F4" align="center">
        	<td align="center"><?php echo $iCounter++;?></td>
        	<td align="center"><?php echo $res[$k]['wing'];?></td>
            <td align="center">
			<?php if($_SESSION['role'] == ROLE_SUPER_ADMIN || $_SESSION['role'] == ROLE_ADMIN || $_SESSION['role']==ROLE_ACCOUNTANT || $_SESSION['role'] == ROLE_MANAGER || ($_SESSION['role'] == ROLE_ADMIN_MEMBER && $_SESSION['profile'][PROFILE_EDIT_MEMBER] == 1))
            {?>
            <a href="view_member_profile.php?scm&id=<?php echo $res[$k]['member_id'];?>&tik_id=<?php echo time();?>&m&view" target="_blank"><?php echo $res[$k]['unit_no']?></a>
            <?php 
			}
			else
			{
				echo $res[$k]['unit_no'];
			}?>
            </td>
            <td align="center"><?php echo $res[$k]['area'];?></td>
			<td align="center"><?php echo str_replace("-", "" ,$res[$k]['flat_configuration']);?></td>
        	<td align="center">
			<?php if($_SESSION['role'] == ROLE_SUPER_ADMIN || $_SESSION['role'] == ROLE_ADMIN || $_SESSION['role']==ROLE_ACCOUNTANT || $_SESSION['role'] == ROLE_MANAGER  || ($_SESSION['role'] == ROLE_ADMIN_MEMBER && $_SESSION['profile'][PROFILE_EDIT_MEMBER] == 1))
            {?>
            <a href="view_member_profile.php?scm&id=<?php echo $res[$k]['member_id'];?>&tik_id=<?php echo time();?>&m&view" target="_blank"><?php echo $res[$k]['owner_name']?></a>
            <?php 
			}
			else
			{
				echo $res[$k]['owner_name'];
			}?>
            </td>
            <td align="center"><a href="#" onClick="window.open('<?php echo $Url; ?>','popup','type=fullWindow,fullscreen,scrollbars=yes');" style="color:#0000FF;"><?php echo $this->obj_utility->getDueAmount($res[$k]['unit_id']);;?></a></td>
            <td align="center"><?php echo $res[$k]['mob'];?></td>
            <td align="center"><a href="mailto:<?php echo $res[$k]['email'];?>" style="color:#0000FF" target="_blank"><?php echo $res[$k]['email'];?></a></td>
           <?php if(IsReadonlyPage() == false && ($_SESSION['role'] == ROLE_SUPER_ADMIN || $_SESSION['role'] == ROLE_ADMIN || $_SESSION['role']==ROLE_ACCOUNTANT || $_SESSION['role'] == ROLE_MANAGER )){?>
            <td align="center">
            <a href="view_member_profile.php?edt&scm&id=<?php echo $res[$k]['member_id'];?>&tik_id=<?php echo time();?>&m&view" target="_blank">
            <img src="images/edit.gif" />
            </a>
            </td>
            
            
            <?php 
			} 
			?>
        </tr>
        <?php }?>
		</tbody>
        </table>
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
		}
	}
}
?>
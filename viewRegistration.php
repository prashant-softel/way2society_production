<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - List of Tenants</title>
</head>


<?php //include_once "ses_set_m.php"; ?>
<?php include_once("includes/head_s.php");
include_once "ses_set_s.php";
include_once("classes/home_s.class.php");
include_once("classes/dbconst.class.php");
include_once("classes/rentingRegistration.class.php");
$objRenting = new rentingRegistration($m_dbConn,$m_dbConnRoot);
$allTenant = $objRenting->getAllTenantByUnitId($_REQUEST['unitId']);
$memberId = $objRenting->getMemberId($_REQUEST['unitId']);
//echo "<pre>";
//print_r($allTenant);
//echo "</pre>";
?>
<div class="panel panel-info" style="margin-top:6%;margin-left:3.5%; border:none;width:70%">
 
    <div class="panel-heading" style="font-size:20px">
        List of Tenants
    </div>
    <br />
    <center>
          <button type="button" class="btn btn-primary" onClick="window.location.href='RentingRegistration.php?type=tenant&unitId=<?php echo $_SESSION['unit_id']?>&View=<?php echo $_REQUEST['View']?>'">Add New Tenant</button>
          </center>
         <!-- <a href="addnotice.php" >Add New</a>-->
	<div class="panel-body">                        
        <div class="table-responsive">
            <table id="example" class="display" cellspacing="0" width="100%">
                <thead>
                    <tr>
                    	 <?php
						if($_SESSION['unit_id'] == "0")
						{
						?>
                        	<th style="width:60px;">Unit No</th>
                        <?php
                       	}
                      	?>
                        <th style="width:120px;">Tenant Name</th>
                        <th style="width:70px;">Contact No</th>
                        <th style="width:70px;">Email Id</th>
                        <th style="width:60px;">Lease Start Date</th>
                        <th style="width:60px;">Lease End Date</th>
                        <th style="width:60px;">Digital Renting Status</th>
                        <th style="width:60px;">Tenant Status</th>
                        <?php if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_ADMIN_MEMBER || $_SESSION['role']==ROLE_SUPER_ADMIN))
	      				{?>
                        <th >Edit</th>
                        <?php }?>
                    </tr>
                </thead>
                <tbody>
                <?php	
            	foreach($allTenant as $k => $v)
           		 {
                 if(strtotime($allTenant[$k]['end_date']) >= strtotime(date('Y-m-d')))
					{?>
					 <tr align="center">
				 <?php
				 	} 
				 else
					{?>
					 <tr  style=" color:#000;">
			   <?php }?> 
                
             		 <?php
						if($_SESSION['unit_id'] == 0)
						{
						?>
                        <td><?php echo $allTenant[$k]['memberName']?></td>
                        <?php
                       	}
                      	?>
                	<td> <a href="tenant.php?mem_id=<?php echo $memberId;?>&view=<?php echo $allTenant[$k]['tenant_id'];?>"><?php echo $allTenant[$k]['tenantName'];?></a></td>
                    <td><?php echo $allTenant[$k]['mobile_no'];?></td>
                	<td><?php echo $allTenant[$k]['email'];?></td>
                    <td><?php echo getDisplayFormatDate($allTenant[$k]['start_date']);?></td>
                    <td><?php echo getDisplayFormatDate($allTenant[$k]['end_date']);?></td>
                	<td>
                    <?php if($allTenant[$k]['tenantStatus'] == "0")
					{
						echo "Completed";
					}
					else if($allTenant[$k]['tenantStatus'] == "1")
					{
						echo "Drafted";
					}
					else if($allTenant[$k]['tenantStatus'] == "2")
					{
						echo "Submitted";
					}
					?>
                    </td>
                    <td>
                    <?php if($allTenant[$k]['active'] == "0")
					{
						echo "Active Tenant";
					}
					else 
					{
						echo "Old Tenant";
					}
					?>
                    </td>
                    
                 
				 <?php 
				   
                   if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_ADMIN_MEMBER || $_SESSION['role']==ROLE_SUPER_ADMIN)) 
				   {?>
					   <td  valign="middle" align="center"> <a href="RentingRegistration.php?type=tenant&unitId=<?php echo $_SESSION['unit_id'] ?>&View=MEMBER&tId=<?php echo $allTenant[$k]['tenant_id'];?>&action=edt" style="color:#000"><img src="images/edit.gif" width="16" /></a></td>
                       <!--<td  valign="middle" align="center"><a href="events.php?deleteid=<?php //echo $events[$k]['events_id'];?>&ev" style="color:#00F"><img src="images/del.gif" width="16"  /></a></td> -->
				   <?php 
				   }
                 
				 }?>
                 </tbody>
            </table>
        </div>
            
</div>

</div>

<?php include_once "includes/foot.php"; ?>

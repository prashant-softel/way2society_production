<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
<title>W2S - Blood Group list</title>
</head>


<?php if(!isset($_SESSION)){ session_start(); } 
  include_once("includes/head_s.php");
include_once("classes/home_s.class.php");
include_once("classes/dbconst.class.php");
include_once("classes/directory.class.php");
$objDirectory = new mDirectory($m_dbConn);
//$result = $objDirectory->FetchUnits();
?>
 <!--<div class="panel panel-info" style="margin-top:6%;margin-left:3.5%; border:none;width:70%;display:block;">
 
    <div class="panel-heading" style="font-size:20px">
     Directory;
    </div>-->
     <div class="panel panel-info" style="margin-top:3.5%;margin-left:3.5%; border:none;width:70%; min-height:450px;">
 
    <div class="panel-heading" style="font-size:20px;text-align:center">Blood Group Listings</div>
   <!-- <div class="panel-body">                        
        <div class="table-responsive">
              <table id="example" class="display" cellspacing="0" width="65%">
                <thead>
                    <tr align="center">
                    	<th>Owner Name</th>
                        <th>Unit No</th>                        
                        <th>Intercom No</th>  
                         <?php  
							if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_ADMIN_MEMBER || $_SESSION['role']==ROLE_SUPER_ADMIN))
	      				{?>          
                        <th>Mobile</th>
                        <th>Email</th>  
                        <?php 
						} ?>                                  
                    </tr>
                </thead>
                <tbody>
                <?php if($result <> '')
				{
					foreach($result as $key=>$val)
					{
				?>
                       <tr>
                       		<td align="left"><?php echo $result[$key]['owner_name'] ?></td>
                            <td><?php echo $result[$key]['unit_no'];?></td>
                            <td><?php echo $result[$key]['intercom_no'];?></td> 
                             <?php  
							if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_ADMIN_MEMBER || $_SESSION['role']==ROLE_SUPER_ADMIN))
	      					{?>
                            <td><?php echo $result[$key]['mob'];?></td> 
                            <td align="left"><a href="mailto:<?php echo $result[$key]['email'];?>" style="color:#0000FF" target="_blank"><?php echo $result[$key]['email'];?></a></td> 
                            <?php 
							} ?>	
                        </tr> 
               <?php } 
				} ?>                                        			
                </tbody>
            </table>
        </div>-->
   <center>
<table align="center" border="0">
<tr>
	<td valign="top" align="center"><font color="red"><?php if(isset($_GET['del'])){echo "<b id=error_del>Record deleted Successfully</b>";}else{echo '<b id=error_del></b>';} ?></font></td>
</tr>
<tr>
<td>
<?php
echo "<br>";
echo $str1 = $objDirectory->BloodGroupListings();
?>
</td>
</tr>
</table>
</center>
        
      </div>
</div>
<?php include_once "includes/foot.php"; ?>
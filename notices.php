<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Notices</title>
</head>

<?php include_once "ses_set_s.php"; ?>
<?php include_once("includes/head_s.php");
include_once("includes/dbop.class.php");
// include_once("RightPanel.php");    
include_once("classes/home_s.class.php");
include_once("classes/dbconst.class.php");
include_once("classes/notice.class.php");
include_once("classes/utility.class.php");
$dbConnRoot = new dbop(true);
$obj_notice = new notice($m_dbConn);
$obj_utility = new utility($m_dbConn, $dbConnRoot);
 
//echo "in:".$_REQUEST['in'];
$display_notices=$obj_notice->FetchAllNotices($_REQUEST['in']);
//echo "<pre>";
//print_r($display_notices);
//echo "</pre>";
$prevID = "";
//print_r($_SESSION);
?>

<div class="panel panel-info" style="margin-top:6%;margin-left:3.5%; border:none;width:70%">
 
    <div class="panel-heading" style="font-size:20px;text-align:center;">
         Notices
    </div>
    <br />
    <?php
	  if($_SESSION['role'] && $_SESSION['is_year_freeze'] == 0 && ($_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['profile']['addnotice.php'] == 1))
	      {?>
          <center><button type="button" class="btn btn-primary" onClick="window.location.href='addnotice.php'">Add New Notice</button></center>
          
         <!-- <a href="addnotice.php" >Add New</a>-->
    <?php }?>
	<div class="panel-body">                        
        <div class="table-responsive">
            <table id="example" class="display" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Issued By</th>
                        <th style="text-align: center;">Subject</th>
                        <th>Cateogory</th>
                        <th>Post Date</th>
                        <th>Expiry Date</th>
                        <!-- <th >View</th> -->
                         <?php if($_SESSION['role'] && $_SESSION['is_year_freeze'] == 0 && ($_SESSION['role']==ROLE_SUPER_ADMIN  || $_SESSION['role']==ROLE_ADMIN_MEMBER || $_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['role'] == ROLE_MANAGER || $_SESSION['profile']['addnotice.php'] == '1' ))
	      				{?>
                        <th>View Activities</th>
                        <th >Edit</th>
                        <th>Delete</th>
                        <?php }?>
                    </tr>
                </thead>
                <tbody>
                <?php if($display_notices <> '')
				{
					//echo "<pre>";
					//print_r($display_notices);
					//echo "</pre>";
					foreach($display_notices as $key=>$val)
					{
						if($prevID != $display_notices[$key]['id'])
						{
							$prevID = $display_notices[$key]['id'];	
							$doc_id = $display_notices[$key]['doc_id'];
							//echo "<br>DocID:".$doc_id;
							$sSubject = $display_notices[$key]['subject'];
							$sDocType = "";
							//echo "<br>DocID new:".$doc_id;
							if($doc_id == "" || $doc_id == "0" || $doc_id == 0)
							{
								//echo "<br>0id:".$prevID;
								$sDocType = "Notice";
							}
							else
							{
								$arDocIDDetails = $obj_utility->GetDocTypeByID($doc_id);
							
								//$sSubject = $display_notices[$key]['subject'];
								$sDocType =  $arDocIDDetails[0]["doc_type"];
							}
							//echo "<br>".$display_notices[$key]['exp_date'];
							if($display_notices[$key]['exp_date'] == "0000-00-00" || strtotime($display_notices[$key]['exp_date']) > strtotime(date('Y-m-d')))
							{
								
							   ?>
							   <tr>
							   <?php 
							} 
						   else
						   {?>
							   <tr  style=" color:#999999;">
							   <?php 
							}?> 
								<td><?php echo $display_notices[$key]['issuedby'];?></td>
								<td align="center"><?php echo "<A href='ViewNotice.php?id=".$display_notices[$key]['id']."'  target='_blank'>".$sSubject."</A>" ?></td>
								<td><?php echo $sDocType; ?></td>
								<td><?php echo getDisplayFormatDate($display_notices[$key]['post_date']);?></td>
                                <?php //if(strtotime($display_notices[$key]['exp_date']) > strtotime(date('Y-m-d')))
								//{?>
								<td>
								<?php //} else { ?>
                               <!--  <td style="color:#F00;"> -->
                                <?php //} 
								 echo getDisplayFormatDate($display_notices[$key]['exp_date']);?></td>
								<?php //if($display_notices[$key]['description'] != "Notice Uploaded")
								//if($display_notices[$key]['note'] == "")
								//{?>
								<?php 
								//}
								//else
								//{
									?>							
									 <!--<td align="center"><?php //echo "<a href='http://way2society.com/Notices/".$display_notices[$key]['note']. "' class='links' target='_blank'>download</a>" ?> </td>-->
							<?php  //}
							 if($_SESSION['role'] &&  $_SESSION['is_year_freeze'] == 0 && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_ADMIN_MEMBER || $_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['role'] == ROLE_MANAGER || $_SESSION['profile']['addnotice.php'] == '1' ))
	      					{?>
                            <td>                            	
                                <a href="viewactivity.php?id=<?php echo $display_notices[$key]['id'];?>"><img src="images/telegram.png" width="20"/></a>
                				<!--<a id="edit" href="javascript:void(0);" onclick="getNotice(this.id, <?php //echo $display_notices[$key]['id'];?>);"><img src="images/edit.gif" /></a> -->
                			</td>
                            <td>                            	
                                <a href="addnotice.php?id=<?php echo $display_notices[$key]['id'];?>"><img src="images/edit.gif" /></a>
                				<!--<a id="edit" href="javascript:void(0);" onclick="getNotice(this.id, <?php //echo $display_notices[$key]['id'];?>);"><img src="images/edit.gif" /></a> -->
                			</td>
                             <td>                            	
                                <a href="addnotice.php?deleteid=<?php echo $display_notices[$key]['id'];?>"><img src="images/del.gif" /></a>                				
                			</td>
                            <?php }?>
                        </tr> 
               <?php 	}
				   } 
				} ?>                                        			
                </tbody>
            </table>
        </div>
            
</div>

</div>

<?php include_once "includes/foot.php"; ?>
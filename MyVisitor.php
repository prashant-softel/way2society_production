<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - My Visitor</title>
</head>

<?php include_once "ses_set_s.php"; ?>
<?php 
$title="W2S - My Visitors";
include_once("includes/head_s.php");
// include_once("RightPanel.php");    
include_once("classes/home_s.class.php");
include_once("classes/dbconst.class.php");
include_once("classes/include/dbop.class.php");
include_once("classes/SM_report.class.php");
$dbConn = new dbop();
$dbConnRoot = new dbop(true);
$smConn = new dbop(false,false,true,false);
$smConnRoot = new dbop(false,false,false,true);
$ObjSMReport = new SM_Report($dbConn,$dbConnRoot,$smConn,$smConnRoot);
//$visitorlist = $ObjSMReport->GetVisitorByUnitID($_SESSION['society_id'],$_SESSION['unit_id']);
if(isset($_REQUEST['type']) && $_REQUEST['type'] == "current")
{
	
	$visitorlist = $ObjSMReport->GetVisitorByUnitID($_SESSION['unit_id'],$_REQUEST['type']);
}

else if(isset($_REQUEST['type']) && $_REQUEST['type'] == "expected")
{
	//$visitorlist = $ObjSMReport->GetVisitorByUnitID($_SESSION['unit_id'],$_REQUEST['type']);
	$Explist = $ObjSMReport->GetExpectedVisitor($_SESSION['unit_id'],$_REQUEST['type']);
}
else if(isset($_REQUEST['type']) && $_REQUEST['type'] =="past")
{
	$visitorlist = $ObjSMReport->GetVisitorByUnitID($_SESSION['unit_id'],$_REQUEST['type']);
}
//var_dump($Explist);
$cnt = 1;
?>
<style>
  #profile_img:hover{
	transform: scale(4);
	  }
</style>
<div class="panel panel-info" style="margin-top:4%;margin-left:1%; width:76%">
 
    <div class="panel-heading" style="font-size:20px;text-align:center;">
     My Visitor
    </div>
    <br/>
<center><button type="button" class="btn btn-primary" onClick="window.location.href='addexpectedvisitor.php'">Add Expected Visitor</button></center>
    <br />
    <div class="panel-body">                        
        <div class="table-responsive">
                    <!-- Nav tabs -->
         <ul class="nav nav-tabs" role="tablist">
        <li <?php echo (isset($_REQUEST['type']) && $_REQUEST['type'] == "current" && $_REQUEST['type'] <>"expected" && $_REQUEST['type'] <> "past" ) ? 'class="active"' : ""; ?>> 
            	<a href="#current" role="tab" data-toggle="tab" onClick="window.location.href='MyVisitor.php?type=current'">Current</a>
    		</li>
            
            <li <?php echo (isset($_REQUEST['type']) && $_REQUEST['type'] == "expected" && $_REQUEST['type'] <> "current" && $_REQUEST['type'] <> "past" ) ? 'class="active"' : ""; ?>> 
            	<a href="#expected" role="tab" data-toggle="tab" onClick="window.location.href='MyVisitor.php?type=expected'">Expected</a>
    		</li>
            
           
            
            <li <?php echo (isset($_REQUEST['type']) && $_REQUEST['type'] == "past") ? 'class="active"' : ""; ?>>
            	<a href="#past" role="tab" data-toggle="tab" onClick="window.location.href='MyVisitor.php?type=past'">Past </a>
    		</li>
        </ul>
		<br/>
            <table id="example" class="display" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Sr No.</th>
                        <?php if($_REQUEST['type'] == "current" || $_REQUEST['type'] == "past" )
						{?>
							<th>Images</th>
                            <th>Visitor Name</th>
                       		<th>Visitor Mobile</th>
                        	<th>Purpose</th>
                        	<th>Company Name</th>                        
                        	<th>Unit No</th> 
                        	<th>In Date</th>
                        	<!--<th>In Time</th>-->
                        	<th>In Gate</th> 
                        	<th>Status</th>     
						<?php
                        }
						else if($_REQUEST['type'] == "expected")
						{?>
							<th>Visitor Name</th>
                       		<th>Visitor Mobile</th>
                            <th>Expected Date</th>
                            <!--<th>Expected Time</th>-->
                        	<th>Purpose</th>
						<?php 
						}
						?>
                        
                                                                                        
                    </tr>
                </thead>
                <tbody>
                	<?php 
					if($_REQUEST['type'] == "current" || $_REQUEST['type'] == "past")
					{
						for($i= 0; $i< sizeof($visitorlist);$i++)
						{
							$imgPath = "SecuirityApp/".$ReportName."/".$GetDetails[$i]['entry_image'];
							?>
							<tr>
                        	<td><?php echo $cnt?></td>
                        	<?php if(($visitorlist[$i]['entry_image'] <> '' || $visitorlist[$i]['entry_image'] <> NULL) && is_file($imgPath)){ ?>
                                    
                             <td style="padding-left:0px;" class="outer-image-hover"><img src="<?php echo $imgPath;?>" class="img-circle" id="profile_img" alt="img" style="width:50px"></td>           
                                <?php }else {?>
                                
                             <td style="padding-left:0px;" class="outer-image-hover"><img src="images/noimage.png" id="profile_img" class="img-circle" alt="img" style=" width:50px"></td>
                             
                            <?php }?>
					 
                        	<td><?php echo $visitorlist[$i]['VName'];?></td>
                        	<td><?php echo $visitorlist[$i]['Contact'];?></td>
                        	<td><?php echo $visitorlist[$i]['purpose'];?></td>
                        	<td><?php echo $visitorlist[$i]['Company'];?></td>
                        	<td><?php echo $visitorlist[$i]['unit_no'];?></td> 
                        	<td><?php echo $visitorlist[$i]['inTimeStamp'];?></td> 
                         	<td><?php echo $visitorlist[$i]['Entry_Gate'];?></td> 
                       	 <td><?php echo $visitorlist[$i]['approvalstatus'];?></td> 
                        <!--<td><?php //echo $visitorlist[$i]['Entry_Gate'];?></td>--> 
                        
                        
                      </tr>
                      
					<?php
					$cnt ++;
						}
					
					}
					else if($_REQUEST['type'] == "expected")
					{
						for($i= 0; $i< sizeof($Explist);$i++)
						{?>
						<tr>
                        	<td><?php echo $cnt?></td>
                            <td><?php echo $Explist[$i]['VisistorName'];?></td>
                        	<td><?php echo $Explist[$i]['mobile'];?></td>
                        	<td><?php echo $Explist[$i]['expected_date'];?></td>
                           <!-- <td><?php //echo $Explist[$i]['expected_time'];?></td>-->
                            <td><?php echo $Explist[$i]['purpose_name'];?></td>
                        </tr>	
						<?php
						$cnt ++; 
						}
					}
					?>
                    
                   
                </tbody>
               
            </table>
        </div>
        
   
      </div>
</div>
<?php /*?><?php
if(isset($_REQUEST['rq']) && $_REQUEST['rq'] <> '')
	{
		?>
			<script>
				getService('delete-' + <?php echo $_REQUEST['rq'];?>);				
			</script>
		<?php
	}
?><?php */?>

<?php include_once "includes/foot.php"; ?>
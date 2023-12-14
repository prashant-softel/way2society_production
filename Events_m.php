
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
<title>W2S - Events</title>
</head>




<?php include_once "ses_set_s.php"; ?>
<?php include_once("includes/head_s.php");
// include_once("RightPanel.php");    
include_once("classes/home_s.class.php");
include_once("classes/dbconst.class.php");
?>
<div class="panel panel-info" style="margin-top:6%;margin-left:3.5%; border:none;width:70%">
 <center>
 <div class="panel panel-info" id="panel" style="display:none">
    <div class="panel-heading" id="pageheader">Events</div>
    <div class="panel-body">                        
        <div class="table-responsive">
        
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th>Issued By</th>
                        <th>Subject</th>
                        <th>Post Date</th>
                        <th>Expiry Date</th>
                        <th>View</th>
                    </tr>
                </thead>
                <tbody>
                	<?php 
					 	  if($_REQUEST['ev'] == 1 || $_REQUEST['ev'] == 0)
						  {?>                	
                	<tr>
                        <td>Society</td>
                        <td>We are celebrating Ganesh Chaturthi festival in our society. We need your some time and effort to celebrate this event,
                        since celebrations for Ganesh Chaturthi are very elaborate and done on a grand scale.</td>
                        <td>25th Sept,2015</td>
                        <td>05th Oct,2015</td>
                        <td>View</td>
                    </tr>
                     <?php
						  }
						  if($_REQUEST['ev'] == 2 || $_REQUEST['ev'] == 0)
						  {
					?>
                    <tr>
                        <td>Society</td>
                        <td>Navaratri is a popular Hindu festival that we can celebrate within our society. Everyones participation is most 
                        important to make this event successfull.</td>
                        <td>15th Oct,2015</td>
                        <td>20th Oct,2015</td>
                        <td>View</td>
                    </tr>
                    <?php
						  }
					?>
                </tbody>
            </table>
        </div>
        <div class="panel-footer">
        Print
        </div>        
</div>

</div>
</center>
</div>
<?php include_once "includes/foot.php"; ?>
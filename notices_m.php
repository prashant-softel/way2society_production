<?php include_once "ses_set_s.php"; ?>
<?php include_once("includes/head_s.php");
// include_once("RightPanel.php");    
include_once("classes/home_s.class.php");
include_once("classes/dbconst.class.php");
?>
<div class="panel panel-info" style="margin-top:6%;margin-left:3.5%; border:none;width:70%">
 
    <div class="panel-heading" style="font-size:20px">
         Notices
    </div>
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
                    <tr>
                        <td>Society</td>
                        <td>Meeting this saturday, 9th May,2015 at Club House</td>
                        <td>6th May,2015</td>
                        <td>10th May,2015</td>
                        <td>View</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="panel-footer">
        Print
        </div>        
</div>

</div>

<?php include_once "includes/foot.php"; ?>
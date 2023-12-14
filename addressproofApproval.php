<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Address Proof Request</title>
</head>

<?php include_once "ses_set_s.php"; ?>
<?php include_once("includes/head_s.php");
// include_once("RightPanel.php");    
include_once("classes/home_s.class.php");
include_once("classes/dbconst.class.php");
include_once("classes/addressProofRequest.class.php");
include_once("classes/include/dbop.class.php");
$m_dbConn = new dbop();
$m_dbConnRoot = new dbop(true);
$objAddressProof = new addressProofApproval($m_dbConn,$m_dbConnRoot);
$sql1 = "select `society_name` from `society` where `society_id` = '".$_SESSION['society_id']."' " ;
$sql1_res = $m_dbConn->select($sql1);
$verificationAccess = $objAddressProof->checkVerificationAccess($_SESSION['role']);
$approvalAccess = $objAddressProof->checkApprovalAccess();
$approval = $objAddressProof->getApprovalLevel();
//var_dump($verificationAccess);
?>
<style>
    .link{display:inline}
    .link {float: left}
	.disabled {
   pointer-events: none;
   cursor: default;
}
</style>
<script>
function Export()
{
	document.getElementById('societyname').style.display ='block';	
	document.getElementById('heading').style.display = 'block';
	window.open('data:application/vnd.ms-excel,' + encodeURIComponent( $("#showTable").html()));
	document.getElementById('societyname').style.display ='none';	
	document.getElementById('heading').style.display = 'none';
}
function PrintPage() 
{
	var originalContents = document.body.innerHTML;
	document.getElementById('societyname').style.display ='block';
	$("a").removeAttr("href");
	var printContents = document.getElementById('showTable').innerHTML;
	console.log(printContents);
	document.body.innerHTML = printContents;
	window.print();

	document.body.innerHTML= originalContents;
}
</script>
<div class="panel panel-info" style="margin-top:4%;margin-left:1%; width:100%">
 
    <div class="panel-heading" style="font-size:20px;text-align:center;">
     Address Proof Request
    </div>
    <br/>
    <br/>
    <div class="panel-body">                        
        <div class="table-responsive">
                    <!-- Nav tabs -->
      	<ul class="nav nav-tabs" role="tablist">
             <li <?php echo (isset($_REQUEST['type']) && $_REQUEST['type'] == "pending") ? 'class="active"' : ""; ?>>
            	<a href="#profile" role="tab" data-toggle="tab" onClick="window.location.href='addressproofApproval.php?type=pending'"><b>Pending Request</b></a>
    		</li>
            <li <?php echo (isset($_REQUEST['type']) && $_REQUEST['type'] == "verified") ? 'class="active"' : ""; ?>> 
            	<a href="#home" role="tab" data-toggle="tab" onClick="window.location.href='addressproofApproval.php?type=verified'"><b>Verified Request</b></a>
    		</li>
            <li <?php echo (isset($_REQUEST['type']) && $_REQUEST['type'] == "approved") ? 'class="active"' : ""; ?>>
            	<a href="#profile" role="tab" data-toggle="tab" onClick="window.location.href='addressproofApproval.php?type=approved'"><b>Completed Request</b></a>
    		</li>
        </ul>
        <?php
			$result = $objAddressProof->getAddressProofRequests($_REQUEST['type']); 
			//var_dump($result);
		?>
		<br/>
        <div id = "btnDiv" name = "btnDiv" style="text-align:center"><input  type="button" id="btnExport" value="Export To Excel"   class="btn btn-primary" onclick="Export()"/>&nbsp;&nbsp;<input  type="button" id="Print" onClick="PrintPage()" name="Print!" value="Print/Export To Pdf" class="btn btn-primary"/>
       </div>
       <div id='showTable' style="font-weight:lighter;" >
       		<center>
        	<div style="display:none;" id = "societyname"><center><h3><font><?php echo $sql1_res[0]['society_name'];?></font></h3></center></div>
            <div style="display:none;" id="heading" ><center><h2><font>Pending Address Proof Requests</font></h2></center></div>
            </center>
    		<table style="text-align:left; width:100%;margin-top:2%" class="table table-bordered table-hover table-striped" cellpadding="50">
        		<thead>
            		<tr style="border:1px solid #ddd;">
                		<th style="border:1px solid #ddd; text-align:center;">Service Request No.</th>
                    	<th style="border:1px solid #ddd; text-align:center;">Flat No.</th>
                    	<th style="border:1px solid #ddd; text-align:center;">Owner Name</th>
                    	<th style="border:1px solid #ddd; text-align:center;">Priority</th>
                    	<th style="border:1px solid #ddd; text-align:center;">Applier Name</th>
                    	<th style="border:1px solid #ddd; text-align:center;">Verified</th>  
                		<?php 
						if($approval == 2)
						{
						?>
                    		<th style="border:1px solid #ddd; text-align:center;">Approved<br />1st Level</th> 
                 			<th style="border:1px solid #ddd; text-align:center;">Approved<br />2nd Level</th>
						<?php 
                   		}
						if($approval == 1)
						{
						?>
                    		<th style="border:1px solid #ddd; text-align:center;">Approved By<br /></th> 
						<?php 
                    	}
						if($approval == 0)
						{
						}	
						?>                                                
             		</tr>
          		</thead>
           		<tbody>
                <?php
				if(sizeof($result) > 0)
				{
					for($i = 0; $i < sizeof($result); $i++)
					{
					?>
                    <tr style="border:1px solid #ddd;vertical-align:central">
                        <td style="border:1px solid #ddd; text-align:center;"><?php echo $result[$i]['request_no']?></td>
                        <td style="border:1px solid #ddd; text-align:center;"><?php echo $result[$i]['unit_no']?></td>
                        <td style="border:1px solid #ddd; text-align:center;"><?php echo $result[$i]['owner_name']?></td>
                        <td style="border:1px solid #ddd; text-align:center;"><?php echo $result[$i]['priority']?></td>
                        <td style="border:1px solid #ddd; text-align:center;"><?php echo $result[$i]['other_name']?></td>
                        <td style="border:1px solid #ddd; text-align:center;"><?php echo $result[$i]['verifiedById']?></td>
                        <?php 
                        if($approval == 2)
                        {
                        ?>
                            <td style="border:1px solid #ddd; text-align:center;"><?php echo $result[$i]['firstApprovalById']?></td>
                            <td style="border:1px solid #ddd; text-align:center;"><?php echo $result[$i]['secondApprovalById']?></td>
                        <?php 
                        }
                        if($approval == 1)
                        {
                        ?>
                            <td style="border:1px solid #ddd; text-align:center;"><?php echo $result[$i]['firstApprovalById']?></td>
                        <?php 
                        }
                        if($approval == 0)
                        {
                        }
                        ?>              
                    </tr>
                	<?php 
					}
				}
				else
				{
				?>
                	<tr style="border:1px solid #ddd;vertical-align:central">
                    	<td style="border:1px solid #ddd;text-align:center" colspan="9">No Records Found.</td>
                    </tr>
                <?php 
				}
				?>
                  
        		</tbody>
     		</table>
    	</div>
    </div>
<?php include_once "includes/foot.php"; ?>
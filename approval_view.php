<?php 
include_once("includes/head_s.php");
include_once ("classes/dbconst.class.php");
include_once("classes/approval_module.class.php");
$objFetchData = new FetchData($dbConn);
$objFetchData->GetSocietyDetails($_SESSION['society_id']);
$obj_approval_module = new approval_module($dbConn, $dbConnRoot);
$display_approval=$obj_approval_module->approval_view($_REQUEST['id']);
$approval=$obj_approval_module->View($_REQUEST['id']);
$attachments= $obj_approval_module->View_attachments($_REQUEST['id']);
?>

<html>
<div id="middle">
<br>
	<div class="panel panel-info" id="panel" style="display:none;margin-top:10px;margin-left:2.5%;width:95%;">
		<div class="panel-heading" id="pageheader">Approvals History</div>
		<br>
		<br>
		<center>
			<button type="button" class="btn btn-primary" onclick="window.location.href='approvals.php?type=active'">Back To Approval List</button>
		</center>
		<br>

		<center>
		<table width="85%" style=" border:1px solid #e7e7e7;" align="center">
		<tr style="background-color:#bce8f1; height:30px">
			<th style="text-align: center;line-height: 30px; width:24%" >Created By</th>
			<th style="text-align: center;line-height: 30px; width:13%">Post Date</th>
			<th style="text-align: center;line-height: 30px; width:13%">Approve By Date</th>
			<th style="text-align: center;line-height: 30px; width:20%">Total Approval Selected</th>
			<th style="text-align: center;line-height: 30px; width:20%">Minimum Approval Required</th>
            <th style="text-align: center;line-height: 30px; width:15%">Status</th>
		</tr>
		<?php
		for($i=0;$i<sizeof($display_approval);$i++)
		{
			if($display_approval[$i]['end_date'] >= date('Y-m-d'))
			{
				$status ="Active";
			}
			else
			{
				$status = "Expire";
			}
		?>
		<tr>
			<td style="text-align: center;line-height: 30px;"><?php echo $_SESSION['name']; ?></td>
			<td style="text-align: center;line-height: 30px;"><?php echo $display_approval[$i]['post_date']; ?></td>
			<td style="text-align: center;line-height: 30px;"><?php echo $display_approval[$i]['end_date']; ?></td>
			<td style="text-align: center;line-height: 30px;"><?php echo $display_approval[$i]['Approvals_selected_count']; ?></td>
            <td style="text-align: center;line-height: 30px;"><?php echo $display_approval[$i]['Approval_required_count']; ?></td>
            <td style="text-align: center;line-height: 30px;"><?php echo $status; ?></td>
		</tr>
		<?php
	 }
	?>
		<tr style="background-color: #bce8f1;font-size: 14px;height: 30px;" >
		<th colspan="6" style="line-height:30px;padding-left: 10px;" >Subjects</th>
		</tr>
		<?php
		for($i=0;$i<sizeof($display_approval);$i++)
		{
		?>
			<tr>
			<td colspan="6" style="text-align: left;padding-top: 10px;padding-bottom: 10px; padding-left:10px; padding-right:10px;"><?php echo $display_approval[$i]['subject']?></td>
			</tr>
		<?php
		}
	?>

	<tr style="background-color: #bce8f1;font-size: 14px;height: 30px;" >
    <th colspan="6" style="line-height:30px;padding-left: 10px;" >Approval Description</th>
	</tr>
	<?php
	for($i=0;$i<sizeof($display_approval);$i++)
	{ 
	?>
		<tr>
		<td colspan="6" style="text-align: left;padding-bottom: 10px;padding-top: 10px;padding-left: 10px;padding-right: 10px;"><?php echo $display_approval[$i]['description']?></td>
		</tr>
	<?php
	}?>
	<tr style="background-color: #bce8f1;font-size: 14px;height: 30px;" >
	<th colspan="6" style="line-height:30px;padding-left: 10px;" >Approval Attachments</th>
	</tr>
	<?php
	if(sizeof($attachments) > 0)
	{
		for($a=0;$a<sizeof($attachments);$a++)
		{
		?>
			<tr>
			<td colspan="6" style="text-align: left;padding-left: 10px;padding-right: 10px;padding-bottom: 5px;padding-top: 5px;">
           <b><a href="<?php echo $attachments[$a]['Url'];?>" target="_blank"><?php echo $attachments[$a]['Doc_name'];?></b></a>
            </td>
			</tr>
		<?php
		}
	}
	else
	{?>
		<tr>
			<td colspan="6" style="text-align: left;padding-left: 10px;padding-right: 10px;padding-bottom: 10px;padding-top: 10px;">No attachments</td>
		</tr>
	<?php 
	}
	?>
	<tr style="background-color: #bce8f1;font-size: 14px;height: 30px;" >
	<th colspan="6" style="line-height:30px;padding-left: 10px;" >Approval Results</th>
	</tr>
    <tr>
    <td colspan="6">
    <table style="width: 100%;">
    <tr style="background-color: #bce8f1;font-size: 14px;height: 30px;" >
		<th style="text-align: center;line-height: 22px; width:30%">Name</th>
		<th style="text-align: center;line-height: 22px; width:10%">Results</th>
		<th style="text-align: center;line-height: 22px; width:10%">Yes</th>
		<th style="text-align: center;line-height: 22px; width:10%">No</th>
        <th style="text-align: center;line-height: 22px; width:40%">Comments</th>
	</tr>
    <?php for($m=0;$m<sizeof($approval);$m++)
	{
		//var_dump($approval);
		$apprved = false;
		$rejected=false;
		if($approval[$m]['Approval_Status'] == 'Yes')
		{
			$apprved = true;
			$rejected=false;
		}
		else if($approval[$m]['Approval_Status'] == 'No')
		{
			$apprved = false;
			$rejected=true;
		}
		?>
    	<tr style="line-height: 22px;">
    	<td align="left" style="padding-left: 10px;padding-right: 5px;"><?php echo $approval[$m]['mem_name']; ?></td>
    	<td align="center"><?php echo $approval[$m]['Vote_Result'];?></td>
    	<td align="center"><?php if($apprved == true)
		{?>
        	<img src="images/clear.png" width="25px" height="25px">
	<?php 
	  }
	  else
	  {
		  echo "--";
	  }?></td>
    	<td align="center"><?php if($rejected == true)
		{?>
        <img src="images/can.png" width="25px" height="25px">
		<?php 
		}
		else
		{
			echo "--";
		}?></td>
        <td align="left" style="padding-left: 5px; padding-right: 5px;"> <?php if($approval[$m]['comments'] <> ''){echo $approval[$m]['comments']; } else { echo "---";}?></td>
    </tr>
    <?php  
	}?>
    </table>
    </td>
    </tr>
	
<tr>
<?php
/*for($k=0;$k<sizeof($approval);$k++)
{ 
?>
<tr>
<td style="text-align: center;line-height: 20px;"><?php echo $approval[$k]['nameck'];?></td>
</tr>
<?php
}*/
?>
</tr>

</table>
</center>

</div>
</div>
<?php include_once "includes/foot.php"; ?>

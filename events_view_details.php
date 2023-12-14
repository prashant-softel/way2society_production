
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>

<title>W2S - Event Details</title>
</head>




<?php include_once("includes/head_s.php");?>

<?php
include_once ("classes/dbconst.class.php");
include_once("classes/events.class.php");
$obj_events = new events($m_dbConn,$m_dbConnRoot);
$events = $obj_events->view_events_details($_REQUEST['id']);
?>
<script type="text/javascript" src="js/OpenDocumentViewer.js"></script>
<body>
<div id="middle">
<br>
<div class="panel panel-info" id="panel" style="display:none;width:70%;margin-left:3.5%;">
<div class="panel-heading" id="pageheader">Event Details</div>
<br>
<center>
	<!--<a href="events_view.php?ev" style="color:#00F"><b>Back to events list</b></a>-->
	<button type="button" class="btn btn-primary" onclick="window.location.href='events_view.php?ev'">Back to events list</button>
</center>
<br>

<?php

foreach($events as $k => $v)
{
	//$startDate = date('Y-m-d');
	//$days = (strtotime($events[$k]['events_date']) - strtotime($startDate)) / (60 * 60 * 24);
	
	//if($days>=0)
	//{
?>
<center>
<table width="95%" style=" border:1px solid #e7e7e7;" align="center">
<tr>
<th align="center" style="line-height: 30px;font-size: 18px;text-align: center;padding-top: 10px; padding-left: 30px;padding-right: 30px;" colspan="5"><?php echo $events[$k]['events_title'];?></th>
</tr>
<tr><td><br></td></tr>
<tr style="background-color:#bce8f1; height:22px">
<th style="text-align: center;line-height: 22px;" >Event Start Date </th>
<th  style="text-align: center;line-height: 22px;">Event End Date </th>
<th  style="text-align: center;line-height: 22px;">Event Time </th>
<th  style="text-align: center;line-height: 22px;">Open TO </th>
<th  style="text-align: center;line-height: 22px;">Participation Charges (Rs.) </th>

</tr>

<tr>
<td align="center"><b><?php echo getDisplayFormatDate($events[$k]['events_date']);?></b></td>
<td align="center"><b><?php echo getDisplayFormatDate($events[$k]['end_date']);?></b></td>
<td align="center"><b><?php echo $events[$k]['event_time'];?></b></td>
<td align="center"><b><?php if($events[$k]['event_type']==1){echo 'All';}else{echo 'Society Members';}?></b></td>
<?php if($events[$k]['event_charges'] > 0)
{?>
<td align="center"><b><?php echo $events[$k]['event_charges'];?></b></td>
<?php }
else
{?>
	<td align="center"><b>Free</b></td>
<?php }
?>
</tr>
<tr><td><br></td></tr>


<tr style="background-color: #bce8f1;font-size: 14px;height: 30px;">
<th  colspan="3" style="line-height:30px;padding-left: 10px;">Event Attachments</th>
<th  colspan="2" style="line-height:30px;padding-left: 10px;">Event Url</th>
</tr>

<tr>
<td colspan="3" style="padding-top: 10px;padding-left: 10px;">
<?php 
		$sVersion = $events[$k]['event_version'];

		$sFileID = $events[$k]['attachment_gdrive_id'];
		$sLink = "";
		if($sVersion == "2")
		{
			if($sFileID == "" || $sFileID == "-")
			{
				$sLink = "Events/". $events[$k]['Uploaded_file'];
			}
			else
			{
				$sLink = "https://drive.google.com/file/d/". $sFileID."/view";
				 $sLink = "https://docs.google.com/viewer?srcid=".$sFileID ."&pid=explorer&efh=false&a=v&chrome=false&embedded=true";
			  	//for view
				//$sLink = "https://drive.google.com/uc?authuser=0&id=". $sFileID;  //for download
			}
		}
		else if($sFileID == "1")
		{
			if($events[$k]['Uploaded_file'] != "")
			{
				$sLink = "Events/". $events[$k]['Uploaded_file'];
			}
			else
			{
				$sLink = "";	
			}
		}

	   if($sLink<>"")
	   {?>
        <div>
      <!-- 	<div style="float:left;"> <img src="images/attpin.png" style="width:20px; float:left;" />&nbsp;&nbsp;&nbsp;<a href="<?php echo $sLink ?>" style="text-decoration:none;"><?php echo $sLink;?>&nbsp;&nbsp;&nbsp;</div> -->
      <div style="float:left;"> <img src="images/attpin.png" style="width:20px; float:left;" />&nbsp;&nbsp;&nbsp;<a title="<?php echo $events[$k]['Uploaded_file']?>" onclick="OpenDocument('<?php echo $sLink ?>')" target="_blank" style="cursor: pointer;text-decoration:none;"><?php echo $events[$k]['Uploaded_file'];?>&nbsp;&nbsp;&nbsp;</div>
      
       	<div style="float: left; margin-top: -8px;"><img src="images/download1.ico" style="width:35px;" /></div>
		<?php 
		}
		?>
</td>


<?php if($events[$k]['events_url']<> "")
{?>
<td colspan="2" style="padding-top: 10px;padding-left: 10px;"><b><a href="<?php echo $events[$k]['events_url'];?>" target="_blank"><?php echo $events[$k]['events_url'];?></b></td>
<?php } ?>
</tr>
<tr style="background-color: #bce8f1;font-size: 14px;height: 30px;" >
<th colspan="5" style="line-height:30px;padding-left: 10px;" >Event description</th>
</tr>
<tr><td colspan="5">
<table style="width:100%; float:left">
<tr>
<td  style="text-align:justify;padding-left: 10px;padding-right: 10px;font-size: 12px;padding-top: 10px;" ><?php echo $events[$k]['events'];?></td>
</tr>
<tr><td><br></td></tr>
</table></td></tr>
<tr><td><br></td></tr>
</table>
</center>
<br><br>
  <?php
	}
?>

<?php include_once "includes/foot.php"; ?>

<?php include_once ("classes/dbconst.class.php"); 
include_once "classes/include/dbop.class.php";
$dbConn = new dbop();
include_once ("classes/include/fetch_data.php");
$objFetchData = new FetchData($dbConn);
$objFetchData->GetSocietyDetails($_SESSION['society_id']);
include_once("classes/notice.class.php");
$obj_notice = new notice($dbConn);
$display_notices=$obj_notice->FetchAllNotices($_REQUEST['id']);
$prevID = "";
//print_r($display_notices);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Notice</title>
<style>
	table,div {
    	border-collapse: collapse;
	}
	table, th, td {
   		border: 0px solid black;
		text-align:left;
	}
	
	
</style>
<script type="text/javascript" src="js/OpenDocumentViewer.js"></script>
</head>
<body>

<div id="bill_header" style="text-align:center; border-style:solid;">
            <div id="society_name" style="font-weight:bold; font-size:18px;"><?php echo $objFetchData->objSocietyDetails->sSocietyName; ?></div>
            <div id="society_reg" style="font-size:14px;"><?php if($objFetchData->objSocietyDetails->sSocietyRegNo <> "")
				{
					echo "Reg. No. ".$objFetchData->objSocietyDetails->sSocietyRegNo;
					echo " ";
					echo "Dated:".$objFetchData->objSocietyDetails->sSocietyRegDate; 
				}
				?>
            </div>
            <div id="society_address"; style="font-size:14px; border-left:thick;"><?php echo 'Reg. Add:'.$objFetchData->objSocietyDetails->sSocietyAddress; ?></div>
</div>
<br/>
<br/>
<!--<div style="text-align:center;">***NOTICE***</div>
<br/>
<br/>-->
<?php 
if($display_notices <> "")
{
	   foreach($display_notices as $key=>$val)
	   {
		?>
     <!--   <div style="text-align:center; text-decoration:underline;">Subject:-<?php echo $display_notices[$key]['subject'];?></div>
		<br/>
		<div style="text-align:left;"> To<br/>All Members of Society</div>
        <br /> -->
        <div style="text-align:left;"><?php
			if($prevID != $display_notices[$key]['id'])
							{
								$prevID = $display_notices[$key]['id'];
								echo $display_notices[$key]['description'];
							}
		 ?></div>
        <br/>
    <!--    <br/>
        Place:-Mumbai
        <br/>
        Posted On:-<?php echo getDisplayFormatDate($display_notices[$key]['post_date']);?>
        <br/>-->
       <br>
       <br> 
       <?php 
		if($display_notices[$key]['notice_version'] == "1")
	    {
       		$AttachmentURL = "Notices/". $display_notices[$key]['note'];
   		}
   		else if($display_notices[$key]['notice_version'] == "2")
   		{
        if($display_notices[$key]['attachment_gdrive_id'] == "" || $display_notices[$key]['attachment_gdrive_id'] == "-")
        {
            $AttachmentURL = "Notices/". $display_notices[$key]['note'];
        }
        else
        {
   			$AttachmentURL = "https://drive.google.com/file/d/". $display_notices[$key]['attachment_gdrive_id']."/view";
        $AttachmentURL = "https://drive.google.com/open?id=".$display_notices[$key]['attachment_gdrive_id']."&authuser=0";
        $AttachmentURL = "https://docs.google.com/viewer?srcid=".$display_notices[$key]['attachment_gdrive_id'] ."&pid=explorer&efh=false&a=v&chrome=false&embedded=true";
        //$AttachmentURL = "https://drive.google.com/open?id=16vtbHpWD66MzMWON1_s7OIKHaIE7euIa"."&authuser=0";
   			//$AttachmentURL = "https://drive.google.com/uc?authuser=0&id=".$display_notices[$key]['attachment_gdrive_id']."&export=download";
        }
   		}
   		else
   		{
   			//unsupported attachment
   		}
   		//echo $AttachmentURL;
	   if($display_notices[$key]['note']<>"")
	   {?>
        <div style="width:50%">
      <!-- <div style="float:left;"> <img src="images/attpin.png" style="width:20px; float:left;" />&nbsp;&nbsp;&nbsp;
        <a href="<?php //echo $AttachmentURL ?>"  style="text-decoration:none;"><?php //echo $display_notices[$key]['note'];?>&nbsp;&nbsp;&nbsp;</div> -->
          <div style="float:left;"> <img src="images/attpin.png" style="width:20px; float:left;" />&nbsp;&nbsp;&nbsp;
        <a title="<?php echo $Title?>" onclick="OpenDocument('<?php echo $AttachmentURL ?>')" target="_blank" style="cursor: pointer;text-decoration:none;"><?php echo $display_notices[$key]['note'];?>&nbsp;&nbsp;&nbsp;</div>
          
       <div style="    float: left; margin-top: -8px;"><img src="images/download1.ico" style="width:35px;" /></a></div>

<?php }  }
}
else
{
	echo "No Notice found.";
}
?>
<br/>
<!--<br/>
<div style="text-align:left; ">Authorized Signatory</div>-->
</div>
     
        
        
</body>
</html>
        
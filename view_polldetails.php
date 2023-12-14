<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Polls Details</title>
</head>

<?php
include_once("includes/head_s.php");
include_once ("classes/dbconst.class.php");
include_once("classes/include/dbop.class.php");
include_once("classes/create_poll.class.php");
include_once("classes/utility.class.php");
$obj_utility=new utility($m_dbConn);
$_REQUEST['rq'] = $obj_utility->decryptData($_REQUEST['rq']);
if( $_REQUEST['rq']==0)
{?>
<script>
	window.location="poll.php";
	</script>
<?php }
$obj_create_poll = new create_poll($m_dbConnRoot,$m_dbConn);
//$utility=$obj_create_poll->getDateDiff();
$details = $obj_create_poll->getViewDetails($_REQUEST['rq']);
$comm_display=$obj_create_poll->getCommentDetails($_REQUEST['rq']);
//print_r($comm_display);
//print_r($details);
 ?>

 <!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Service Request Details</title>
</head>-->

<div class="panel panel-info" id="panel" style="margin-top:6%;margin-left:3.5%; border:none;width:70%;display:none">
<div class="panel-heading" id="pageheader" style="font-size:20px">
    Polls Details
    </div>
    <br />
    	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
    <!--<script type="text/javascript" src="js/jquery.min.js"></script>-->
	<script type="text/javascript" src="js/ajax_new.js"></script>
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/create_poll.js"></script>
    <script language="javascript" type="application/javascript">
	function go_error()
    {
        setTimeout('hide_error()',10000);	
    }
    function hide_error()
    {
        document.getElementById('error').style.display = 'none';	
    }	
	
	
</script>
  
<style>
.skill-bar{
	background: rgb(52, 152, 219);
	
	   
}</style>

<style>
.scrollit {
    overflow-y: auto;
    height:200px;
}</style>

<body>

<?php //echo "role".$_SESSION['role'];?>
<center><!--<a href="servicerequest.php">Go Back</a>-->
<div style="padding-left: 15px;padding-bottom: 10px;"><button type="button" class="btn btn-primary btn-circle" onClick="history.go(-1);" style="float:left;" id="btnBack"><i class="fa  fa-arrow-left"></i></button>
		<!-- <center> <INPUT TYPE="button" id="Print" onClick="printTable()" name="Print" value="Print"   class="btn btn-primary"></center>--></div>
</center>


<br>
<br>
<?php 
//print_r($details);
//if($details <> "")
	  { ?>
<div width="100%" style="font-size:12px;" id="PrintableDiv"  >
<div id="society_name" style="font-weight:bold; font-size:18px; display:none;"><?php  //echo $objFetchData->objSocietyDetails->sSocietyName; ?></div>
   
<table width="100%" style="font-size:12px;" id="PrintableTable">
<tr><td colspan="10"><br /></td></tr>   
    <tr style="background-color:#bce8f1;font-size:14px;"  height="25">
    	<th colspan="6"  align="left">&nbsp;&nbsp; Question</th>
   	</tr>
    
    <tr>
    	<td colspan="10"><b style="float:left;margin-left: 20px;margin-top: 10px;"><?php echo $details[0]['question'];?></b></td>
    </tr>
    <tr><td colspan="10"><br /></td></tr>
	<tr  style="background-color:#bce8f1;font-size:14px;" height="25">
          <th style="width:20%;"><center>Group Name</center></th>
          <th style="width:15%;"><center>Start Date</center></th>
        <th style="width:15;"><center>End Date</center></th>
        <th style="width:15%;"><center>Active Duration</center></th>   
        <th style="width:20%"><center>View Attachment</center></th>
        <th style="width:15%;"><center>Total Votes</center></th>
        <!--<th style="width:20%;">Photo</th>-->
    </tr>
    <tr>
    <?php 
	$totalvote=0;
	for($i=0;$i<sizeof($details);$i++)
		{  
			//$options=$details[$i]['options'];
			$counter=$details[$i]['counter'];
			$totalvote=$totalvote+$counter;
			//echo $totalvote;
		//print_r($options);
			//var_dump($options);
		}?>
    	
    	<td align="center"><?php echo $details[0]['group_name'];?></td>
        <td align="center"><?php echo $details[0]['start_date'];?></td>
        <td align="center"><?php echo $details[0]['end_date'];?></td>
        <td align="center"><?php echo $details[0]['date'];?> Days</td>
        <?php if($details[0]['file_id'] <> '' || $details[0]['file_id'] <> 0){?>
        <td align="center"><a href="https://way2society.com/poll/<?php echo $details[0]['file_id'];?>" target="_blank">View </a></td>
        <?php }else {?>
        <td align="center">No Attachment </td>
        <?php }?>
        <td align="center"><?php echo  $totalvote;?></td>
        <td align="center"><?php //echo $details[0]['category'];?></td>
      <!--  <td align="center"><a href="<?php// echo substr($details[0]['img'],3);?>" class="fancybox"><img src="<?php// echo substr($details[0]['img_thumb'],3);?>" height="100" width="100" /></a></td>-->
        
    </tr> 
    <!--<tr><td colspan="10"><br /></td></tr>   
    <tr style="background-color:#bce8f1;font-size:14px;"  height="25">
    	<th colspan="5"  align="left">&nbsp;&nbsp; Question</th>
   	</tr>
    <tr>
    	<td colspan="10"><b style="float:left;margin-left: 20px;margin-top: 10px;"><?php echo $details[0]['question'];?></b></td>
    </tr>-->
    <tr><td colspan="10"><br /></td></tr> 
    <tr style="background-color:#bce8f1;font-size:14px;" height="25">
    	<th colspan="10" align="center"><center>Vote Saved</center> </th>      
    </tr>
    <table width="100%" style="font-size:12px;" id="PrintableTable">
    <tr style="background-color:#ddd;font-size:14px;" >
    <th style="width:38%;">&nbsp;&nbsp;Options</th>
        <th style="width:15%;">&nbsp;&nbsp;Votes</th>
        <th style="width:50%;">&nbsp;&nbsp;Percentage</th></tr>
        <tr>
         <?php 
	// print_r($poll_optoin);
	  
	  
		//$poll=explode(',', $options);	 
		for($i=0;$i<sizeof($details);$i++)
		{  
			$options=$details[$i]['options'];
			$counter=$details[$i]['counter'];
			//$totalvote=$totalvote+$counter;
			$total=$counter / $totalvote;
			$value=number_format( $total * 100 );
			//echo $value;
			  		?>
        <tr><td ><b style="float:left;margin-left: 20px;margin-top: 10px;"><?php echo $options;?></b></td>
    	<!--<tr><td ><b style="float:left;margin-left: 20px;margin-top: 10px;"><?php echo $options;?></b></td>-->
        <td ><b style="float:left;margin-left: 20px;margin-top: 10px;"><?php echo $counter;?></b></td>
        <td ><div style="float:left;margin-left: 20px;margin-top: 10px; background: #eee; width:80%;"><div class="skill-bar" style="width:<?php echo $value;?>%;">&nbsp;</div></div><span style="margin-top:10px;float: left; margin-left: 5px;"><?php echo $value;?> %</span>
        </td></tr>
   <?php }?></table>    </div>

 	<tr><td colspan="10"><br /></td></tr>
    <?php if($_SESSION['role'] && $_SESSION['is_year_freeze'] == 0 && ($_SESSION['role']==ROLE_SUPER_ADMIN ||$_SESSION['role']==ROLE_ADMIN))
	      {?>
                
	
      <table width="100%" style="font-size:12px;">    
          <tr><td colspan="10"><br /></td></tr>  
    <tr  style="background-color:#bce8f1;font-size:14px;" height="25">
          <th style="width:20%;"><center>UnitID</center></th>
          <th style="width:20%;"><center>Member Name</center></th>
          <th style="width:15%;"><center>Vote</center></th>
        <th style="width:35%;"><center>Comment</center></th>
        <!--<th style="width:20%;">Photo</th>-->
    </tr></table>
   <div class="scrollit" style="overflow-x:auto;" id="">
   <table width="100%" style="font-size:12px;"> 
  
   
      <tr>
   
   
     <?php 
	// print_r($poll_optoin);
	  
	  
		//$poll=explode(',', $options);	 
		
		foreach($comm_display as $k => $v)
		{ ?>
        
           
         <?php
            $Uno=$comm_display[$k]['UnitNo'];
			$MName=$comm_display[$k]['owner_name'];
			$Status=$comm_display[$k]['option'];
			$Comment=$comm_display[$k]['Comment'];?>
			
		
			  		
                     
    	<td style="width:20%;" align="center"><?php echo $Uno; ?></td>
        <td style="width:20%;" align="center"><?php echo $MName; ?></td>
        <td style="width:15%;" align="center"><?php echo $Status; ?></td>
        <td style="width:35%;" align="center"><?php if($Comment<>''){echo $Comment;}
		
		else{echo "-";}?></td> 
        <tr><td><br /></td></tr> 
       </tr>
          
    <?php } ?> 
   
    <?php } ?>
      
        <br><br></td>
        </table></div>
        
        
        
    </tr></table></div></body></div>

</div>

<?php include_once "includes/foot.php"; ?>
<?php }?>        
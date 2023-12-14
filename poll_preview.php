<?php 
include_once("includes/head_s.php");
include_once ("classes/dbconst.class.php");
include_once("classes/include/dbop.class.php");
include_once("classes/create_poll.class.php");
include_once("classes/utility.class.php");
$obj_utility=new utility($m_dbConn);
//echo $_REQUEST['rq'];
$_REQUEST['rq'] = $obj_utility->decryptData($_REQUEST['rq']);
if( $_REQUEST['rq']==0)
{?>
<script>
	window.location="polls.php";
	</script>
<?php }
//else{
$obj_create_poll = new create_poll($m_dbConnRoot,$m_dbConn);
$details = $obj_create_poll->getPollList($_REQUEST['rq']);
$voteCount=$obj_create_poll->getVoteList($_REQUEST['rq']);
$comment_display=$obj_create_poll->getComment($_REQUEST['rq']);
//print_r($comment_display);
//$list = $obj_create_poll->getViewDetails($_REQUEST['rq']);
//print_r($voteCount);
//$date=$obj_utility->getPeriodBeginAndEndDate($details[0]['start_date'],$details[0]['end_date']);
$date = $obj_utility->getDateDiff(getDBFormatDate($details[0]["end_date"]), date("Y-m-d"));
 //$days = $obj_utility->getDateDiff($details[0]["end_date"], $details[0]["start_date"]);

//print_r($date);
?>
<html>

<head>
<link rel="stylesheet" type="text/css" href="css/pagination.css" >
<style>
.opt,.ques
{
	border:0px solid;
	border-color:#3399CC;
	padding: 10px;
    font-size: 12px;
	text-transform: capitalize;
}
</style>
<title>Voting</title>
<!-- The JavaScript makes the pop-up window. -->
<script type="text/javascript">
window.name = "parent";
function voteForma(revote)
{ 
//  /var=
//var poll_id = document.getElementById('option').value;
var poll_id=document.getElementById('id').value;
var comment_rev=document.getElementById('comment_rev').value;
if(document.querySelector('input[name="answer"]:checked') !=null)
{
var option_id=document.querySelector('input[name="answer"]:checked').value;

//alert(option_id);
  //alert(revote);
 
 //var _id;
	
	$.ajax({
				
			url : "ajax/create_poll.ajax.php",
			type : "POST",
			data : {"method" : 'answer',"pollID" : poll_id, "optionID" : option_id, "comment_rev" : comment_rev ,"Re-Vote": revote},
			success : function(data)
			{	
			//tabBtnClicked('view.php?id=' + album_id + '&photo=1');	
				 //document.write ("User wants to continue!");
				 location.reload(true);	
				   //document.getElementById('msg').innerHTML = "Your Vote has been Register Successfully";
				   
			},
		});

	//iden=str.split("-");	
	//window.location.href='addclassified.php?edt&id='+iden[1];
	}
	else
	{
	alert("Please select vote option");	
	//return false;
	}
}
</script>
<script>
function showoptions()
{
document.getElementById('showrevote').style.display="none";
document.getElementById('option_div').style.display="block";
}
</script>

<style>
.skill-bar{
	background: rgb(52, 152, 219);
	}
	.tillDate
	{
	
	}

</style>
<!-- The JavaScript makes the pop-up window. -->
</head>

<div class="panel panel-info" id="panel" style="margin-top:6%;margin-left:3.5%; border:none;width:70%;display:none">
<div class="panel-heading" id="pageheader" style="font-size:20px">
    Poll Details
    </div>
    <br />
    <div class="panel-body">
<center>

<div style="width: 60%; border-radius:4px" class="panel panel-default">
<div class="panel-heading"><span style="font-size:22px"><?php echo $details[0]['question'];?></span></div>
<!--<div id="msg" style="color: blue; font-size: 13px;font-weight: bold; padding-top: 0px;" ></div>-->
<div style="padding-top: 20px; width:95%;" class="">

<?php 

		$bShowOptions = false;
		$bShowMessage = false;
		$bShowResult = false;
		
		//Show Options
		//echo $date;
		if($date >= 0 && (($details[0]['allow_vote'] ==0 && sizeof($voteCount) == 0) || ($details[0]['allow_vote'] == 1)))
		{
			$bShowOptions = true;
		}
		
		//Show Message
		//if($date < 0 || ($details[0]['allow_vote'] == 0 && sizeof($voteCount) > 0))
		if($date < 0 || (sizeof($voteCount) > 0))
		{
			$bShowMessage = true;
		}
		
		//Show Result
		if($date < 0 ||  ($details[0]['poll_status'] == 1) || ($details[0]['poll_status'] == 2 && sizeof($voteCount) > 0))
		{
			$bShowResult = true;
		}
		
		$sSelecte =  (sizeof($voteCount) > 0) ? $voteCount[0]['options'] : '' ;
		$bVoted = (sizeof($voteCount) > 0) ? true : false;
		
		//echo sizeof($details);
		if($bShowOptions == true)
		{
			?>
            <div id="option_div" style="display:<?php echo ($details[0]['allow_vote'] == 1 && sizeof($voteCount) > 0) ? "none" : "" ; ?>;">
         

                <table  width="100%" align=center border=1  cellpadding=5 class="normaltext" style=" BORDER-COLLAPSE: collapse; padding:5px;border-radius:4px;border:none" >
    <input type="hidden"  id="id" value="<?php echo $_REQUEST['rq'];?>">
                <!--<tr style="display:none"><th class="ques" style="background-color:#009;color:#FFF;font-weight:bold"><?php //echo $details[0]['question'];?></th></tr>-->
                <tr><td  style="padding-top: 0px;  float: left;margin-left: 120px; font-weight: bold; ">Voting line closes on &nbsp; : &nbsp;<span style="color:#00F"><?php echo getDisplayFormatDate($details[0]['end_date']) ;?></span></td><!--<td style="padding-top: 10px; float: right; margin-right: -185px;    font-weight: bold; color:#00F"><?php// echo getDisplayFormatDate($details[0]['end_date']) ;?></td>--></tr>
                <tr><td><br></td></tr>
                <tr><td style=" font-size: 13px;float: left;margin-left: 15px;"><?php echo $details[0]['additional_content']?></td></tr>
                <?php if($details[0]['file_id'] <> 0 || $details[0]['file_id'] <> ''){?>
                 <tr><td style=" font-size: 13px;float: left;margin-left: 15px;">Please check  <a href="http://localhost/beta_awsamit/poll/<?php echo $details[0]['file_id'];?>" target="_blank" style="text-decoration:none;">   Attachment</a></td></tr>
                 <?php }?>
                <tr><td><br><br></td></tr>
                <?php
                for($i=0;$i<sizeof($details);$i++)
                {
                    $options=$details[$i]['options'];
                    $options1=$details[$i]['option_id'];
                    $selected='';
                    if(sizeof($voteCount)>0  && $voteCount[0]['option_id']==$options1)
                    {
                         $selected="checked";
                         $sSelecte=$options;
                    }
                    ?>
                    <tr><td align="left" class="opt">
                        <input type="radio" style="font-size:18px;background-color:transparent; box-shadow: 0px 0px 0px #666;    width: 4%; height: .9em;" name="answer" id="option" class="answer" <?php  echo $selected; ?>  value="<?php echo $options1;?>"><!--<label for="option">&nbsp;&nbsp;&nbsp;  <?php echo $options;?></label>--><span style="text-transform: capitalize; font-size:14px;">&nbsp;&nbsp;&nbsp;  <?php echo $options;?></span></td>
                        <?php 
                                //if ($i==0 )
                                //{
                                    //?>
                                    <!--<td rowspan="<?php //echo sizeof($details); ?>" style="padding-top: 10px;  float: right;margin-right: 70px; font-weight: bold;">Voting line closes on &nbsp; : &nbsp;</td><td style="padding-top: 10px; float: right; margin-right: -185px;    font-weight: bold; color:#00F"><?php //echo getDisplayFormatDate($details[0]['end_date']) ;?></td>	-->
                                    <?php 
                                //}
                } 
            ?>
      </tr>
      <tr>
      <td>
      <b><?php echo $details[0]['comment_question']; ?></b>
      <?php if($details[0]['comment_question']<>'')
	  {?>
      
      </td></tr>
      <tr>
      <td><textarea name="comment_rev" id="comment_rev" rows="3" cols="58"></textarea></td></tr>
      <?php }else{?>
      <tr>
      <td><textarea name="comment_rev" id="comment_rev" rows="3" cols="58" style="visibility:hidden"></textarea></td>
		  
		 <?php }?>
      </tr>
   
   
    </table>
    <br><br>
            <?php 
            if($details[0]['allow_vote']==1 && sizeof($voteCount)> 0)
            {?>
           
    <input type="hidden" id="id" name="id" ><input type="submit" name="insert" id="insert" value="Re-vote" class="btn btn-primary" style="color:#FFF; width:100px;background-color:#337ab7;" onClick="voteForma(<?php echo  $details[0]['allow_vote']; ?>);">
               <!-- <p style="color:#F00;  font-size: 16px; font-weight: bold; padding-top: 15px; padding-bottom: 0px;">Your vote has been registered successfully </p>	
                                <p style="font-size:12px; font-weight:bold;"> Your selctection was &nbsp;&nbsp;:&nbsp;&nbsp;<?php echo $sSelecte;?></p>-->
                  
        <?php }
                else{?>
            <input type="hidden" id="id" name="id" ><input type="submit" name="insert" id="insert" value="Vote" class="btn btn-primary" style="color:#FFF; width:100px;background-color:#337ab7;" onClick="voteForma(<?php echo  $details[0]['allow_vote']; ?>);">
                <?php
                } ?>
                <p>&nbsp;</p>
                </div>
                <?php 
		}?>
		
        
	
	<?php
		if($bShowMessage == true)
		{
				if(sizeof($voteCount)>0)
				{  
	 					?>
                          <!--<div>
									 <span >Start date : <?php// echo getDisplayFormatDate($details[0]['start_date']);?></span>
                                     <span>End date : <?php// echo getDisplayFormatDate($details[0]['end_date']);?></span> 
                                 </div>-->
                                 
							<input type="hidden" id="id" name="id" ><span style="color:#F00;  font-size: 16px; font-weight: bold;">Your vote has been registered successfully </span>	
							<p style="font-size:12px; font-weight:bold; padding-top: 20px;padding-bottom: 30px;"> Your selctection was &nbsp;&nbsp;:&nbsp;&nbsp;<?php echo $sSelecte;?></p>
 <?php if($comment_display<>'')
 {?>
<p style="font-size:12px; font-weight:bold;padding-bottom:10%"> Your Review &nbsp;&nbsp;:&nbsp;&nbsp;<?php strip_tags(print_r($comment_display)); ?></p>
<?php }else
{?>
      <p style="font-size:12px; font-weight:bold;padding-bottom:10%"> Your Review &nbsp;&nbsp;:&nbsp;&nbsp;<?php echo "No Comments" ?></p>  <?php } ?>                                              
							<?php 
								if($date < 0)
								{?>
                          			  <p style="font-size:12px; font-weight:bold; padding-top: 10px;padding-bottom: 10px;color:#F00;">Voting line closed on <br><?php echo getDisplayFormatDate($details[0]['end_date']);?></p>     
						<?php 
								}
								else
								{?>
                               <div style="width: 97%; padding-left: 3%; height:40px;">
									 <div style="float: left;width: 44%;font-size: 12px;font-weight: bold;">Poll start : <span style="color:#00F;"><?php echo getDisplayFormatDate($details[0]['start_date']);?></span></div>
                                     <div style=" float: left;width: 44%;font-size: 12px;font-weight: bold;margin-left: 44px;">Poll close : <span style="color:#F00"><?php echo getDisplayFormatDate($details[0]['end_date']);?></span></div> 
                                 </div>
                                 
                                 
								<?php }
				}
				else
			    {
						?>
							<span style="color:#F00;  font-size: 16px; font-weight: bold;">You did not vote for this poll</span>
							<p style="font-size:12px; font-weight:bold; padding-top: 10px;padding-bottom: 10px;color:#F00;">Voting line closed on <br><?php echo getDisplayFormatDate($details[0]['end_date']);?></p>
						<?php 	
				}
		}
	?>
		

<?php 
if($bShowResult)
{
?>
<p>&nbsp;</p>
<div style="padding-top: 0px; width:95%;">
<table width="100%" style="font-size:12px;   background-color: rgba(217, 237, 247, 0.18);" id="PrintableTable">
    <tr style="background-color:#bce8f1;font-size:14px;" >
    <th style="width:35%;">Options</th>
        <th style="width:15%;">Votes</th>
        <th style="width:55%;">Percentage</th></tr>
           <?php 
	  $totalvote=0;
	  for($i=0;$i<sizeof($details);$i++)
	  {
		  $counter=$details[$i]['counter'];
		    $totalvote=$counter+$totalvote;
		}	 
		for($i=0;$i<sizeof($details);$i++)
		{  
			$options=$details[$i]['options'];
			$counter=$details[$i]['counter'];
		  //  $totalvote=$counter+$totalvote;
			$total=$counter / $totalvote;
			$value=number_format( $total * 100 );
			  		?>
        <tr><td ><b style="float:left;margin-left: 20px;margin-top: 10px;"><?php echo $options;?></b></td>
    	<!--<tr><td ><b style="float:left;margin-left: 20px;margin-top: 10px;"><?php// echo $options;?></b></td>-->
        <td ><b style="float:left;margin-left: 20px;margin-top: 10px;"><?php echo $counter;?></b></td>
        <td ><div style="float:left;margin-left: 6px;margin-top: 10px; background: #eee; width:75%;"><div class="skill-bar" style="width:<?php echo $value;?>%;">&nbsp;</div></div><span style="margin-top:10px;float: left; margin-left: 5px;"><?php echo $value;?> %</span>
        
        <?php } ?>
        <table width="100%" align="right"  style="  padding-top: 20px; ">
        <tr><td align="right"><b style="font-size: 13px;">Total votes &nbsp; :&nbsp;&nbsp;</b> </td><td><b style="font-size: 13px;"><?php echo $totalvote;?></b></td></tr>
      <tr><td><br></td></tr>
        </table></td></tr>
         </table>
         <br>
       <?php 
		if($details[0]['allow_vote']==1 && sizeof($voteCount)> 0 && $date > 0)
		{?>  
    <!--   <div style="float:right; font-size:12px; font-weight:bold;"><a  id="showrevote" onClick="showoptions()">Re-vote</a></div>-->
       <input type="button" onClick="showoptions()" value="Re-Vote" id="showrevote" class="btn btn-primary" style="color:#FFF; width:80px;background-color:#337ab7;">
               <?php }?>
        <p>&nbsp; </p>
        </div>
<?php  }?>
</div>
</div>
</center>
</div>
</div>
</html>
<?php //}?>
<?php include_once "includes/foot.php"; ?>

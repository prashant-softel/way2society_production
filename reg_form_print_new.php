<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Service  Provider Profile View</title>
</head>

<?php //include_once "ses_set_common.php"; ?>
<?php 
/*if(isset($_SESSION['admin']))
{
	include_once("includes/header.php");
}
else if(isset($_SESSION['sadmin']))
{
	*/
	include_once("includes/head_s.php");
/*}
else
{
	include_once("includes/header_m.php");
}
*/
$actual_link = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
include_once("classes/service_prd_reg_edit.class.php");
$obj_service_prd_reg = new service_prd_reg($m_dbConn, $m_dbConnRoot);

if(isset($_REQUEST['id']))
{
	if($_REQUEST['id']<>"")
	{
		$edit = $obj_service_prd_reg->reg_edit($_REQUEST['id']);
	}
}
//echo "URL : ".$_SERVER['HTTP_HOST'];
//echo "url2 : ".substr($edit[0]['photo'],3);
include_once("classes/add_comment.class.php");
$obj_add_comment = new add_comment($m_dbConn, $m_dbConnRoot);
include_once ("dbconst.class.php"); 
/*
if(isset($_POST['submit']))
{
	$add_comment = $obj_add_comment->add_comment();
}
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>

<link rel="stylesheet" type="text/css" href="css/pagination.css" >
<script type="text/javascript" src="js/ajax.js"></script>
<!--<script type="text/javascript" src="js/jsadd_comment.js"></script>-->
<script language="javascript" type="application/javascript">
function go_error()
{
	setTimeout('hide_error()',6000);	
}
function hide_error()
{
	document.getElementById('error').style.display = 'none';	
}
function go_error1()
{
	setTimeout('hide_error1()',6000);	
}
function hide_error1()
{
	document.getElementById('error_del').style.display = 'none';	
}

function val()
{
	var comment = document.getElementById('comment').value;
		
	if(comment == '')
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter your comment";
		document.getElementById("comment").focus();
		
		go_error();
		return false;
	}
}

function del_comment(str)
{
	var conf = confirm("Are you sure ??? you want to delete it ??");
	if(conf==true)
	{
		remoteCall("ajax/del_comment.php","del_comment=del_comment&del_comment=del_comment&del_comment=del_comment&str="+str,"res_del_comment");	
	}
}

function res_del_comment()
{
	var res = sResponse;
	window.location.href = 'reg_form_print_new.php?id=<?php echo $_REQUEST['id'];?>&del&srm&tm=<?php echo time();?>#view';
		
}
</script>
<style>
#middle11 
{
	margin-top:15px;
	margin-bottom:15px;

	width:500px;
	background:#fff;
	border: 1px solid #ff6f00;
	border-radius: 7px;
	-moz-border-radius: 7px;
	-webkit-border-radius: 7px;
	-moz-box-shadow: 3px 3px 9px #666;
	-webkit-box-shadow: 3px 3px 9px #666;
	box-shadow: 3px 3px 9px #666;
	padding:2px 0 0 0;
}
</style>

<!------------------------------------------------------------------------------->
<!------------------------------------------------------------------------------->

	<!-- Add jQuery library -->
	<script type="text/javascript" src="lib/jquery-1.7.2.min.js"></script>

	<!-- Add mousewheel plugin (this is optional) -->
	<script type="text/javascript" src="lib/jquery.mousewheel-3.0.6.pack.js"></script>

	<!-- Add fancyBox main JS and CSS files -->
	<script type="text/javascript" src="source/jquery.fancybox.js?v=2.0.6"></script>
	<link rel="stylesheet" type="text/css" href="source/jquery.fancybox.css?v=2.0.6" media="screen" />

	<!-- Add Button helper (this is optional) -->
	<link rel="stylesheet" type="text/css" href="source/helpers/jquery.fancybox-buttons.css?v=1.0.2" />
	<script type="text/javascript" src="source/helpers/jquery.fancybox-buttons.js?v=1.0.2"></script>

	<!-- Add Thumbnail helper (this is optional) -->
	<link rel="stylesheet" type="text/css" href="source/helpers/jquery.fancybox-thumbs.css?v=1.0.2" />
	<script type="text/javascript" src="source/helpers/jquery.fancybox-thumbs.js?v=1.0.2"></script>

	<!-- Add Media helper (this is optional) -->
	<script type="text/javascript" src="source/helpers/jquery.fancybox-media.js?v=1.0.0"></script>
    
    <script type="text/javascript">
		$(document).ready(function() {			
		
		//Simple image gallery. Uses default settings
		$('.fancybox').fancybox();
		$('.fancybox1').fancybox();
	
	
		//Button helper. Disable animations, hide close button, change title type and content
		$('.fancybox-buttons').fancybox({
		openEffect  : 'none',
		closeEffect : 'none',

		prevEffect : 'none',
		nextEffect : 'none',

		closeBtn  : false,

		helpers : {
			title : {
				type : 'inside'
			},
			buttons	: {}
		},

			afterLoad : function() {
				this.title = 'Image ' + (this.index + 1) + ' of ' + this.group.length + (this.title ? ' - ' + this.title : '');
			}
		});
		});
	</script>
	<style type="text/css">
		.fancybox-custom .fancybox-skin 
		{
			box-shadow: 0 0 50px #222;
		}
	</style>
<!------------------------------------------------------------------------------->
<!------------------------------------------------------------------------------->

</head>

<?php if(isset($_REQUEST['msg'])){ ?>
<body onLoad="go_error();">
<?php }else if(isset($_REQUEST['del'])){ ?>
<body onLoad="go_error1();">
<?php }else{ ?>
<body>
<?php } ?>

<div id="middle" style="width:70%;">

<center><h2 style="padding:0;"><b>Service Provider Profile View</b></h2></center>
<center><a href="reg_form_print1.php?id=<?php echo $_REQUEST['id'] ?>" id="prt1"><img src="images/print.png" width="40" height="40" /></a></center><br>
<?php 
if( $edit[0]['active']>0)
{
if($_SESSION['role'] == ROLE_SUPER_ADMIN || $_SESSION['role'] == ROLE_ADMIN_MEMBER || $_SESSION['role'] == ROLE_ADMIN )
{?>
<center><button type="button" class="btn btn-primary" onclick="window.open('printcert.php?id=<?php echo $_REQUEST['id'] ?>')">Print Id Card</button>
&nbsp;&nbsp;&nbsp;&nbsp;
<?php }
}?>
<button type="button" class="btn btn-primary" onclick="window.location.href='#view'">View Comment</button>
&nbsp;&nbsp;&nbsp;&nbsp;
<?php if(isset($_REQUEST['other'])){?>
<button type="button" class="btn btn-primary" onclick="window.location.href='service_prd_reg_view_other.php?srm'">Back to list</button>
<?php }else{?>
<button type="button" class="btn btn-primary" onclick="window.location.href='service_prd_reg_view.php?srm'">Back to list</button>
<?php }?>
</center>
<br>

<center>
<table align="center" border="0" style="width:100%;">
<tr bgcolor="#CCCCCC">
	<th colspan="2" height="35">Personal Details</th>
</tr>
<tr bgcolor="#BDD8F4" align="left">
<td>
    <table align="center" border="0">
    <tr>
  <!--  <td rowspan="5" align="center"><a href="<?php echo $_SERVER['HTTP_HOST'].substr($edit[0]['photo'],14);?>" class="fancybox"><img src="<?php echo substr($edit[0]['photo_thumb'],14);?>" height="100" width="100" /></a></td>-->
        <td rowspan="5" align="center"><a href="<?php echo substr($edit[0]['photo'],3);?>" class="fancybox"><img src="<?php echo substr($edit[0]['photo_thumb'],3);?>" height="100" width="100" /></a></td>
   <!-- </tr>
    <tr> -->
        <th width="80">Full Name</th>
        <td width="10">:</td>
        <td width="130"><?php echo stripslashes($edit[0]['full_name']);?></td>
    </tr>
    <tr>
        <th>Date of Birth</th>
        <td>:</td>
        <td><?php echo strtoupper($edit[0]['dob']);?></td>
    </tr>
    <tr>
        <th>Age</th>
        <td>:</td>
        <td><?php echo strtoupper($edit[0]['age']);?> Year</td>
    </tr>
    <tr>
        <th>Education</th>
        <td>:</td>
        <td><?php echo stripslashes($edit[0]['education']);?></td>
    </tr>
    <tr>
        <th>Married</th>
        <td>:</td>
        <td><?php echo stripslashes($edit[0]['marry']);?></td>
    </tr>
    </table>
</td>
<td valign="top">
	<?php 
	//echo $combo_cat_id = $obj_service_prd_reg->combobox1111("select cat_id,cat from cat where status='Y' order by cat","cat_id[]","cat_id","select cat_id from spr_cat where service_prd_reg_id='".$_REQUEST['id']."' and status='Y'"); 
	?>
     <table>
        <tr>
            <td><b>Categories</b></td>
            <td>:</td>            
        	<td>
        <?php
			$categories = $obj_service_prd_reg->fetchSelectedCategories($_REQUEST['id']);
			for($i = 0; $i < sizeof($categories); $i++)
			{
				echo $categories[$i]['cat'];
				if($i < sizeof($categories) - 1)
				{
					echo ', ';
				}
			}
		?>
        	</td>
        </tr>
        <tr>
        	<th width="135">Working Since</th>
            <td width="10">:</td>
            <td width="200"><?php  if($edit[0]['since']<>""){echo getDisplayFormatDate(stripslashes($edit[0]['since']));}else{ echo 'Not Mentioned';}?></td>
        </tr>
        <tr>
        	<th>Identification Marks</th>
            <td>:</td>
            <td align="justify"><?php echo stripslashes($edit[0]['identy_mark']);?></td>
        </tr>
        <tr>
        	<th width="130">Address</th>
            <td width="10">:</td>
            <td width="450"><?php echo stripslashes($edit[0]['cur_resd_add']);?></td>
        </tr>
        <tr>
        	<th>Contact No.</th>
            <td>:</td>
            <td><?php echo stripslashes($edit[0]['cur_con_1']);?> <?php if($edit[0]['cur_con_2']<>""){echo ' & '.stripslashes($edit[0]['cur_con_2']);}?></td>
        </tr>
    </table>
</td>
</tr>
<tr bgcolor="#BDD8F4" align="left">
	<td colspan="2">
    	<table align="center" border="0" style="width:100%;">    	
        <tr>
        	<th style="width:20%;">Native Address</th>
            <td style="width:5%;">:</td>
            <td style="width:25%;"><?php echo stripslashes($edit[0]['native_add']);?></td>        
        	<th style="width:20%;">Native Contact No.</th>
            <td style="width:5%;">:</td>
            <td style="width:25%;"><?php echo stripslashes($edit[0]['native_con_1']);?> <?php if($edit[0]['native_con_2']<>""){echo ' & '.stripslashes($edit[0]['native_con_2']);}?></td>
        </tr>
        <tr>
        	<th>Reference Name</th>
            <td>:</td>
            <td><?php echo stripslashes($edit[0]['ref_name']);?></td>        
        	<th>Reference Contact No.</th>
            <td>:</td>
            <td><?php echo stripslashes($edit[0]['ref_con_1']);?> <?php if($edit[0]['ref_con_2']<>""){echo ' & '.stripslashes($edit[0]['ref_con_2']);}?></td>
        </tr>
         <tr>
        	<th>Reference Address</th>
            <td>:</td>
            <td colspan="4"><?php echo stripslashes($edit[0]['ref_add']);?></td>
        </tr>
        </table>
    </td>
</tr>
<tr bgcolor="#CCCCCC">
	<th colspan="2" height="35">Family Details</th>
</tr>
<tr>
	<td colspan="2">
    <center>
    <table align="center" border="0" style="width:100%;">
        <tr height="30" bgcolor="#A4A4FF">
        	<th  style=" text-align:center; width:33%;">Relation</th>
            <th style=" text-align:center; width:33%;">Full Name</th>
            <th style=" text-align:center; width:33%;">Occupation</th>
        </tr>
        
        <tr height="25"  bgcolor="#BDD8F4">
        	<td align="center">Father</td>
            <td align="center"><?php if($edit[0]['father_name']<>""){echo stripslashes($edit[0]['father_name']);}else{ echo 'Not Mentioned';}?></td>
            <td align="center"><?php if($edit[0]['father_occ']<>""){echo stripslashes($edit[0]['father_occ']);}else{ echo 'Not Mentioned';}?></td>
        </tr>
        
        <tr height="25"  bgcolor="#BDD8F4">
        	<td align="center">Mother</td>
            <td align="center"><?php if($edit[0]['mother_name']<>""){echo stripslashes($edit[0]['mother_name']);}else{ echo 'Not Mentioned';}?></td>
            <td align="center"><?php if($edit[0]['mother_occ']<>""){echo stripslashes($edit[0]['mother_occ']);}else{ echo 'Not Mentioned';}?></td>
        </tr>
        
        <tr height="25"  bgcolor="#BDD8F4">
        	<td align="center">Husband / Wife</td>
            <td align="center"><?php if($edit[0]['hus_wife_name']<>""){echo stripslashes($edit[0]['hus_wife_name']);}else{ echo 'Not Mentioned';}?></td>
            <td align="center"><?php if($edit[0]['hus_wife_occ']<>""){echo stripslashes($edit[0]['hus_wife_occ']);}else{ echo 'Not Mentioned';}?></td>
        </tr>
        
        <tr height="25"  bgcolor="#BDD8F4">
        	<td align="center">Son / Daughter</td>
            <td align="center"><?php if($edit[0]['son_dou_name']<>""){echo stripslashes($edit[0]['son_dou_name']);}else{ echo 'Not Mentioned';}?></td>
            <td align="center"><?php if($edit[0]['son_dou_occ']<>""){echo stripslashes($edit[0]['son_dou_occ']);}else{ echo 'Not Mentioned';}?></td>
        </tr>
        
        <tr height="25"  bgcolor="#BDD8F4">
        	<td align="center">Other</td>
            <td align="center"><?php if($edit[0]['other_name']<>""){echo stripslashes($edit[0]['other_name']);}else{ echo 'Not Mentioned';}?></td>
            <td align="center"><?php if($edit[0]['other_occ']<>""){echo stripslashes($edit[0]['other_occ']);}else{ echo 'Not Mentioned';}?></td>
        </tr>
        </table>
        </center>
    </td>
</tr>
<tr bgcolor="#CCCCCC">
	<th height="35" style="width:50%;">Supporting Document Attached</th>
    <th height="35"> &nbsp; Units where service provider works </th>
</tr>
<tr bgcolor="#BDD8F4" align="left">
	<td valign="top">
    	<table>
    <?php //echo $combo_cat_id = $obj_service_prd_reg->combobox1111("select document_id,document from document where status='Y' order by document","document[]","document","select document_id from spr_document where service_prd_reg_id='".$_REQUEST['id']."' and status='Y'"); 
		$documents = $obj_service_prd_reg->fetchSelectedDocs($_REQUEST['id']);	
		for($i = 0; $i < sizeof($documents); $i++)
		{
	?>	   
       		<tr> 
            	<td> <?php echo $documents[$i]['document']; ?> </td> 
                <td> &nbsp; &nbsp; <?php 
				if($documents[$i]['attached_doc'] <> "")
				{
					//echo "<a href='http://way2society.com/Service_Provider_Documents/".$documents[$i]['attached_doc']. "' class='links'>download</a>"  ;
					echo "<a href='Uploaded_Documents/".$documents[$i]['attached_doc']. "' class='links'>download</a>"  ;
				}
				 ?> </td> 
			</tr>
     <?php
		} ?>
       </table> 
    </td>
    <td valign="top">
    <?php echo $combo_cat_id = $obj_service_prd_reg->combobox_units($_REQUEST['id']); ?>
    </td>
</tr>
</table>
</center>
<br>
<center>
<table style="width:100%;" align="center"><tr><td><hr /></td></tr></table>
</center>
<br>
<center><a href='#add' name="add" style="visibility:hidden;" >Add Comment</a><br><br>

<form method="post"  name="AddComment" action="process/reg_form_print_new.process.php">
<input type="hidden" name="id" value="<?php echo $_GET['id'];?>"/>
<input type="hidden" name="comment_id" id="comment_id"/>
<center>
<table align="center" border="0" style="width:100%;">
<tr>
	<td valign="top" align="center"><font color="red"><?php if(isset($_GET['msg'])){echo "<b id=error>Record Added Successfully</b>";}else{echo '<b id=error></b>';} ?></font></td>
</tr>
<tr align="center">
	<td>Hide My Name : <input type="checkbox" name="hide_name" value="1" /></td><br>
</tr>	
<tr align="center">
	<td><textarea name="comment" placeholder="ENTER YOUR COMMENT HERE" id="comment" rows="2" cols="100"></textarea></td>
</tr>	
<tr><td></td></tr>
<tr>
    <td align="center"><input type="submit" name="submit" id="insert" value="Submit" style="color: #fff;background-color: #337ab7;border-color: #2e6da4;width:11%;height:20%;margin-top:2px"
></td>
</tr>
</table>
</center>
</form>
<center>
<table align="center" border="0" style="width:100%;"><tr><td><hr /></td></tr></table>
</center>
<br />
<center>
<h3 style="margin: 0;padding: 0;">COMMENTS</h3>
<a href='#view' name="view" style="visibility:hidden;" >Comments</a>
<table align="center" border="0" width="720">
<tr>
	<td valign="top" align="center"><font color="red"><?php if(isset($_GET['del'])){echo "<b id=error_del>Comment deleted Successfully</b>";} ?></font></td>
</tr>
<tr>
<td>
<?php
echo "<br>";
$str1 = $obj_add_comment->pgnation();
/*echo "<br>";
echo $str = $obj_add_comment->display1($str1);
echo "<br>";
$str1 = $obj_add_comment->pgnation();
echo "<br>";*/
?>
</td>
</tr>
</table>
</center>
</div>
<?php include_once "includes/foot.php"; ?>
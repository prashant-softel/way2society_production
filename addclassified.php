<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Add New Classified</title>
</head>

<?php
include_once("includes/head_s.php");
include_once("classes/addclassified.class.php");
include_once ("classes/dbconst.class.php");
include_once("classes/include/dbop.class.php");
$obj_classified = new classified($m_dbConnRoot,$m_dbConn);
$details = $obj_classified->member();
//$edit=$obj_classified->geteditclassified();

//print_r($_SESSION);
$UnitBlock = $_SESSION["unit_blocked"];
if(isset($_REQUEST['id']))
{
	if($_REQUEST['id']<>"")
	{
		$edit = $obj_classified->reg_edit($_REQUEST['id']);
		//for($i=0;$i <= sizeof($edit)-1; $i++)
			//{ 
			$image=$edit[0]['img'];
			$image_collection = explode(',', $image);	
			//}
	//	    $image=$result[$i]['img'];
//			$image_collection = explode(',', $image);
			//print_r ($image_collection);
			  //echo substr($edit[0]['image_collection']);
			 //echo "<img src='ads/".$image_collection[0]."'>;";
	}
}
?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
    <!--<script type="text/javascript" src="lib/js/jquery.min.js"></script>-->
	
<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/addclassified.js"></script>
    <script type="text/javascript" src="ckeditor/ckeditor.js"></script>
    <script language="javascript" type="application/javascript">
	function go_error()
    {
		$(document).ready(function()
		{
			$("#error").fadeIn("slow");
		});
        setTimeout('hide_error()',8000);	
    }
    function hide_error()
    {
		$(document).ready(function()
		{
			$("#error").fadeOut("slow");
		});
    }
	
	//$( document ).ready(function() {
		var isblocked = '<?php echo $UnitBlock ?>';
		if(isblocked==1)
		{
			//alert("We are sorry,but your access has been blocked for this feature . Please contact your Managing Committee for resolution .");
			window.location.href='suspend.php';
		}
    //});
	
	</script>
    <script type="text/javascript">
        $(function()
        {
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker({ 
            dateFormat: "dd-mm-yy", 
            showOn: "both", 
            buttonImage: "images/calendar.gif", 
            buttonImageOnly: true 
        })});
  </script>
  
 
  <style type="text/css" media="all">
			a, a:link, a:active, a:visited {
				color: #337ab7;
   				 text-decoration: none;
   				/* font-weight: bold;*/
			}
			a:hover {
				color: blue;
			}

			div.box {
				border: 1px solid #ccc;
				height: 55px;
				padding: 5px 7px 10px 7px;
 				overflow: hidden; 
				width:170px;
    			
			}
			div.resize {
				padding-bottom: 250px;
			}
			div.resize div.box {
				position: absolute;
				width: 50%;
				height: 100px;
			}
			div.resize div.box.before {
				right: 50%;
				margin-right: 10px;
			}
			div.resize div.box.after {
				left: 50%;
				margin-left: 10px;
			}
			div.box.opened
			{
				height: auto;
			}
			div.box .toggle .close,
			div.box.opened .toggle .open
			{
				display: none;
			}
			div.box .toggle .opened,
			div.box.opened .toggle .close
			{
				display: inline;
			}
			div.box.before {
				background-color: #ffeeee;
			}
			div.box.after {
				background-color: rgba(217, 237, 247, 0.28);
			}
			p.before {
				color: #990000;
			}
			p.after {
				color: #006600;
			}
			div.box.pathname {
				height: auto;
			}
			.pathname {
				height: 25px;
			}

</style>

 
</head>
<?php if((isset($_POST['ShowData']) && $_POST['ShowData']<> '')  || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])){ ?>
<body onLoad="go_error();">
<?php }else
{
	?>
    <body>
<?php } ?>
<br>
<div id="middle">
<div class="panel panel-info" id="panel" style="display:block; width:77%;">
        <div class="panel-heading" id="pageheader">Add New Classified</div>
<center>
<br><br>
<br>
<center><button type="button" class="btn btn-primary" onclick="window.location.href='my_listing_classified.php'">Go Back</button></center>
<br>


<?php
$star = "<font color='#FF0000'>*</font>";
if(isset($_REQUEST['msg']))
{
	$msg = "Sorry !!! You can't delete it. ( Dependency )";
}
else if(isset($_REQUEST['msg1']))
{
	$msg = "Deleted Successfully.";
}
else{}
?>


<form name="classified" id="classified" method="post" action="process/addclassified.process.php" enctype="multipart/form-data" onSubmit="return val();">
<table align='center'>
<?php
//print_r($_POST);
if(isset($_POST['ShowData']))
	{ //echo "1";
?>
		<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $_POST['ShowData']; ?></b></font></td></tr>
<?php
	}
	else
	{//echo "2";
	?>
		<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $msg; ?></b></font></td></tr>
	<?php
	}

?>
<!--<br><br>
<div id="ErrorDiv2" style="display:block;font-weight:bold; color:#F00;font-size: 12px;"><?php echo $_REQUEST['error'] ?></div><br>-->
<tr align="left">
        <td valign="middle"><?php //echo $star;?></td>
        <th><b>Post By :</b></th>
        <td>&nbsp; : &nbsp;</td>
        <td id="post_by" ><?php echo $_SESSION['name'];?></td>
	</tr>
    <tr><td colspan="4"><input type="hidden" name="post_by"  value="<?php echo $_SESSION['name'];?>"> </td></tr>
		 <tr align="left">
        <td valign="middle"><?php echo $star;?></td>
        <th><b>Category Name</b></th>
        <td>&nbsp; : &nbsp;</td>
        <td><select name="cat_id" id="cat_id" style="font-size:12px;">
<!--select `id`,`name` from `album` where status='Y' order by id desc ", $_REQUEST['id'] -->
<?php echo $select_cate = $obj_classified->combobox1("select `cat_id`, `name` from classified_cate where status='Y'", 0, false);?>
</select>
        </td>
	</tr>      
			 <tr align="left">
       <td valign="middle"><?php echo $star;?></td>
        <th><b>Classified Title</b></th>
        <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="ad_title" id="ad_title" /></td>
		</tr>
        
       <tr align="left">
       <td valign="middle"><?php echo $star;?></td>
        <th><b>Location</b></th>
        <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="location" id="location" /></td>
		</tr>
        <tr align="left">
        <td valign="middle"><?php echo $star;?></td>
        <th><b>Contact No </b></th>
        <td>&nbsp; : &nbsp;</td>
        <td><input type="text" name="phone" value="<?php echo $details[0]['mob'];?>" id="phone"  onKeyPress="return blockNonNumbers(this, event, true, true);"/></td>
	</tr>                     
        <tr align="left">
       <td valign="middle"><?php echo $star;?></td>
        <th><b>Email</b></th>
        <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="email" value="<?php echo $details[0]['email'];?>" id="email" /></td>
		</tr>
		<!--<tr>
			<td>Description</td>
			<td><textarea name="desp" id="desp"></textarea></td>
		</tr>-->
         
		<tr>
			 <tr align="left">
        <td valign="middle"></td>
        <th><b>Active Date</b></th>
        <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="act_date" id="act_date" value="<?php echo date('d-m-Y');?>"  class="basics" size="10" readonly  style="width:80px;" /></td>
		</tr>
		<tr>
			 <tr align="left">
        <td valign="middle"><?php echo $star;?></td>
        <th><b>Expity Date</b></th>
        <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="exp_date" id="exp_date" class="basics" size="10" readonly  style="width:80px;" /></td>
		</tr>
		<!--<tr>
			<td>Image</td>
			<td><input type="text" name="img" id="img" /></td>
		</tr>-->
         <tr id="upload"> 
        <td valign="middle"></td>
        <td><b>Upload Image</b></td>   
        <td>&nbsp; : &nbsp;</td>
        <td>
        <table><tr><td>
         <!--<input type="hidden" name="img_old" id="img_old" value="<?php //$image_collection[0]?>"/>-->               
        <input name="img[]" id="img" type="file" accept=".jpg, .png, .jpeg, .gif" multiple /> <!--<a id="noticename" style="visibility:hidden;" target="_blank"> View Uploade  </a>--></td>
        <td>
        <?php 
		for($i=0;$i<sizeof($image_collection);$i++)
		{
			if(strlen($image_collection[$i]) >0 )
			{
		?>
        
		<a href="ads/<?php echo $image_collection[$i];?>"><img  style="width:50px; height:35px;" src="ads/<?php echo $image_collection[$i]?>"></a><a href="javascript:void(0);" onClick="del_photo('<?php echo $image_collection[$i];?>',<?php echo $_REQUEST['id']?>);"><img style="width: 15px;margin-top: -30px; margin-left: -10px;" src="images/del.gif" /></a>
      <?php
	   }
		}
	  ?>
       </td></tr></table>
       
      </td>
    </tr>  
     <?php
	    if($_SESSION['role'] == ROLE_SUPER_ADMIN || $_SESSION['profile']['#classified.php'] == '1')
	   {?>
        <tr  align="left">
        <td valign="middle"><?php //echo $star;?></td>
			<td><b>Approved by society</b></td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="checkbox" name="varified" id="varified" value="1" >	</td>
		</tr>
       <tr><td colspan="4">&nbsp;</td></tr>
       <tr  align="left">
        <td valign="middle"><?php //echo $star;?></td>
			<td><b>Notify By SMS </b></td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="checkbox" name="ClassifiedSMS" id="ClassifiedSMS" value="1" ><b> &nbsp; &nbsp;( Charges Apply )</b>	</td>
		</tr>
<?php }?>

        <tr><td colspan="4">&nbsp;</td></tr>
   <tr id="create"><td valign="middle"><?php echo $star;?></td>
    <th style="text-align:left;"><b>Description</b></th>
    <td colspan="2">&nbsp; : &nbsp;</td>
        <tr><td colspan="4"><textarea name="desp" id="desp" rows="5" cols="50"></textarea></td></tr>
       	<script>
			//CKEDITOR.config.height = 100;
			//CKEDITOR.config.width = 500;
			CKEDITOR.config.extraPlugins = 'justify';
			CKEDITOR.replace('desp', {toolbar: [
         						{ name: 'clipboard', items: ['Undo', 'Redo']},{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align'], items: [ 'NumberedList', 'BulletedList','JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
        						{name: 'editing', items: ['Format', 'Bold', 'Italic', 'Underline', 'Strike'] }
   								 ],
								 height: 100,
        						 width: 700,
								 uiColor: '#14B8C4'
								 });
		</script>
        
        
        
     <tr><td colspan="4">&nbsp;</td></tr>
    
		<!--<tr>
			<td colspan="2" align="center"><input type="hidden" name="id" id="id"><input type="submit" name="insert" id="insert" value="Insert" style="background-color:#E8E8E8;"></td>-->
            
            <tr>
		<td colspan="4" align="center"><input type="hidden" id="id" name="id" ><input type="submit" name="insert" id="insert" value="Submit" class="btn btn-primary" style="color:#FFF; width:100px;background-color:#337ab7;">&nbsp;&nbsp;&nbsp;&nbsp;
        <button type="button" class="btn btn-primary" onClick="submitComment()" id="comments"  style="color:#FFF; width:100px;background-color:#337ab7;  box-shadow: 1px 1px 4px #666;padding: 2px 4px 2px 4px;">Comment </button></td>
    </tr>
	<tr><td colspan="4">&nbsp;</td></tr>	
</table>
</form>

<br />
<form name="classified" id="classified" method="post" action="process/addclassified.process.php?id=<?php echo $_REQUEST['id'];?>" onSubmit="return validate();"><center>

<table style="border:1px solid #CCC; width:75%;font-size:12px; padding:10px; border-radius: 15px; display:none" id="comment">
<?php

	if(isset($_POST["ShowData"]))
		{
?>
			<tr height="30"><td colspan="3" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $_POST["ShowData"]; ?></b></font></td></tr>
<?php   }
		else
		{?>
			<tr height="30"><td colspan="3" align="center"><font color="red" style="size:11px;"><b id="error"></b></font></td></tr>
<?php 	} ?>   
	<tr>
		<th style="width:20%;">&nbsp; Changed By</th>
        <td style="width:10%;">&nbsp; : &nbsp;</td>
        <td style="width:70%;"><?php echo $_SESSION['name'];?></td>
    </tr>
    <tr><td colspan="3"><input type="hidden" name="changedby" id="changedby" value="<?php echo $_SESSION['name'];?>" /></td></tr>
    <tr>
    	<th>&nbsp; Status</th>
        <td>&nbsp; : &nbsp;</td>
        <td>
        	<select id="status" name="status">
            	<!--<option value="0"> Please Select </option>-->
                <?php
				
				if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_ADMIN_MEMBER || $_SESSION['role']==ROLE_SUPER_ADMIN))
	      		{?>
                <option value="Pending" <?php if($details[sizeof($details)-1]['status']=='N'){?> selected <?php }?>> Pending </option>
               <option value="Approved" <?php if($details[sizeof($details)-1]['status']=='Y'){?> selected <?php }?>> Approved </option>
                <option value="Not Approved" <?php if($details[sizeof($details)-1]['status']=='N'){?> selected <?php }?>> Not Approved </option>
               <?php } ?>
               </select>
	    </td>
    </tr>
    <tr><td colspan="3"><br /></td></tr>
    <tr>       
        <th>&nbsp; Comments</th>
        <td>&nbsp; : &nbsp;</td>
        <td><textarea name="comments" id="comments" rows="6" cols="60"></textarea></td>
	</tr>
    <tr><td colspan="3"><br /><!--<input type="hidden" id="unit" name="unit" value="<?php //echo $_SESSION['unit_id'] ?>">--></td></tr>
    <tr align="center">
    	<td colspan="3"><input type="submit" name="submit" id="submit" value="Submit Comments"  class="btn btn-primary"/> </td>
    </tr>
    <tr><td colspan="3"><!--<input type="hidden" name="emailID" id="emailID" value="<?php //echo $loginID[0]['member_id']; ?>" />--></td></tr>
        <tr><td colspan="3"><!--<input type="hidden" name="CLEmailIDs" id="CLEmailIDs" value="<?php //echo $strCLEMailIDs; ?>" />--></td></tr>
     
    <tr><td colspan="3"><br /></td></tr>      
</table> 
<br>
</center> 
</form>      

</center>

</body>
</html>
<?php 
if(isset($_GET['edt']))
{
	?>
    	<script>
			geteditclassified('edit-<?php echo $_GET['id']?>');	
		</script>
	<?php
}
?>
<script>
 function submitComment()
{ 
	var x = document.getElementById('comment');
	if (x.style.display === 'none') {
        x.style.display = 'block';
    } else {
        x.style.display = 'none';
    }
	// document.getElementById('comment').style.display="block";
}

 </script>
<?php include_once "includes/foot.php"; ?>
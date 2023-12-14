<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - My Classified List</title>
</head>

<?php
include_once("includes/head_s.php");
include_once("classes/addclassified.class.php");
include_once ("classes/dbconst.class.php");
include_once("classes/include/dbop.class.php");
$obj_classified = new classified($m_dbConnRoot,$m_dbConn);
$details = $obj_classified->member();
//print_r($_SESSION);
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
<script>

 function Bysearch(value)
 { 
 //alert(value);
 window.location.href='my_listing_classified.php?src&cat_id='+value;
 
 }

</script>
 
</head>
<?php if((isset($_POST['ShowData']) && $_POST['ShowData']<> '')  || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])){ ?>
<body onLoad="go_error();">
<?php }else if(isset($_REQUEST['edt']) || $_REQUEST['insert']=='Edit'){ ?>
<body onLoad="getsociety('edit-<?php echo $_SESSION['society_id'];?>');">
<?php }else if(isset($_REQUEST['show'])){ ?>
<body onLoad="getsociety('show-<?php echo $_REQUEST['id'];?>');">
<body>
<?php } ?>
<br>
<div id="middle">
<div class="panel panel-info" id="panel" style="display:block; width:77%;">
        <div class="panel-heading" id="pageheader">My Classified List</div>
<center>
<br><br>
<br>
<div style="float: left;width: 100%;">
<div style="float: left;margin-left: 2%;"><button type="button" class="btn btn-primary" onclick="window.location.href='classified.php'">Go Back</button></div>
<div style="float: right;margin-right: 2%;"><button type="button" class="btn btn-primary" onclick="window.location.href='addclassified.php'">Add new classified</button></div>
</div>
<br>

<br>

 <div>
 <span style="font-family:sans-serif; font-size:13px;font-size: 15px;float: left;margin-left: 2px;margin-top: 22px;">Search by Category :&nbsp; </span><br/>
<select name="cat_type" id="cat_type" style="font-size:12px;float: left;margin-top: 12px; margin-left: 0px;" onChange=" Bysearch(this.value);" >
<?php echo $show_category = $obj_classified->combobox("select `cat_id`,`name` from classified_cate",$_GET['cat_id']);?>
 </select>
</div>
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


<!--<form name="classified" id="classified" method="post" action="process/addclassified.process.php" enctype="multipart/form-data" onSubmit="return val();">
<table align='center'>-->
<?php
/*//print_r($_POST);
if(isset($_POST['ShowData']))
	{ //echo "1";*/
?>
		<!--<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php //echo $_POST['ShowData']; ?></b></font></td></tr>-->
<?php
	/*}
	else
	{//echo "2";*/
	?>
		<!--<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php// echo $msg; ?></b></font></td></tr>-->
	<?php
	//}

?>
<!--<br><br>
<div id="ErrorDiv2" style="display:block;font-weight:bold; color:#F00;font-size: 12px;"><?php echo $_REQUEST['error'] ?></div><br>-->
<!--<tr align="left">
        <td valign="middle"><?php //echo $star;?></td>
        <th><b>Post By :</b></th>
        <td>&nbsp; : &nbsp;</td>
        <td id="post_by" ><?php// echo $_SESSION['name'];?></td>
	</tr>-->
   <!-- <tr><td colspan="4"><input type="hidden" name="post_by"  value="<?php echo $_SESSION['name'];?>"> </td></tr>
		 <tr align="left">
        <td valign="middle"><?php// echo $star;?></td>
        <th><b>Category Name</b></th>
        <td>&nbsp; : &nbsp;</td>-->
       <!-- <td><select id="cat_type" name="cat_type"><option value="" >Select</option>
            <option value="sale">Sale</option>
            <option value="rental">Rental</option>
            <option value="classes">Classes</option>
            <option value="other">Other</option>
            </select>
        </td>
	</tr>     --> 
			<!-- <tr align="left">
       <td valign="middle"><?php echo $star;?></td>
        <th><b>Post Title</b></th>
        <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="ad_title" id="ad_title" /></td>
		</tr>-->
        
      <!-- <tr align="left">
       <td valign="middle"><?php// echo $star;?></td>
        <th><b>Location</b></th>
        <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="location" id="location" /></td>
		</tr>-->
       <!-- <tr align="left">
        <td valign="middle"><?php //echo $star;?></td>
        <th><b>Phone :</b></th>
        <td>&nbsp; : &nbsp;</td>
        <td><input type="text" name="phone" value="<?php// echo $details[0]['mob'];?>" id="phone"/></td>
	</tr>       -->              
        <!--<tr align="left">
       <td valign="middle"><?php// echo $star;?></td>
        <th><b>Email</b></th>
        <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="email" value="<?php// echo $details[0]['email'];?>" id="email" /></td>
		</tr>-->
		<!--<tr>
			<td>Description</td>
			<td><textarea name="desp" id="desp"></textarea></td>
		</tr>-->
         
		<!--<tr>
			 <tr align="left">
        <td valign="middle"></td>
        <th><b>Active Date</b></th>
        <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="act_date" id="act_date" value="<?php //echo date('d-m-Y');?>"  class="basics" size="10" readonly  style="width:80px;" /></td>
		</tr>
		<tr>-->
			<!-- <tr align="left">
        <td valign="middle"><?php// echo $star;?></td>
        <th><b>Expity Date</b></th>
        <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="exp_date" id="exp_date" class="basics" size="10" readonly  style="width:80px;" /></td>
		</tr>-->
		<!--<tr>
			<td>Image</td>
			<td><input type="text" name="img" id="img" /></td>
		</tr>-->
         <!--<tr id="upload"> 
        <td valign="middle"></td>
        <td><b>Upload Image</b></td>   
        <td>&nbsp; : &nbsp;</td>               
        <td><input name="img[]" id="img" type="file" accept=".jpg, .png, .jpeg, .gif" multiple /> <a id="noticename" style="visibility:hidden;" target="_blank"> View Uploade  </a></td>
    </tr>   -->
       <!-- <tr><td colspan="4">&nbsp;</td></tr>
   <tr id="create"><td valign="middle"><?php echo $star;?></td>
    <th style="text-align:left;"><b>Description</b></th>
    <td colspan="2">&nbsp; : &nbsp;</td>
        <tr><td colspan="4"><textarea name="desp" id="desp" rows="5" cols="50"></textarea></td></tr>-->
      <!-- 	<script>
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
		</script>-->
        
        
    <!--    
     <tr><td colspan="4">&nbsp;</td></tr>
   -->
		<!--<tr>
			<td colspan="2" align="center"><input type="hidden" name="id" id="id"><input type="submit" name="insert" id="insert" value="Insert" style="background-color:#E8E8E8;"></td>-->
           <!-- <tr>
		<td colspan="4" align="center"><input type="hidden" id="id" name="id" ><input type="submit" name="insert" id="insert" value="Submit" style=" width:100px; height:30px; background-color:#D6D6D6; "></td>
    </tr>-->
		
<!--</table>
</form>
-->


<table align="center">
<tr>
<td>
<?php
if(isset($_GET['src']))
{
echo "<br>";

$str = $obj_classified->pgnation1($_GET['cat_id']);
echo "<br>";
}
else{
	$str = $obj_classified->pgnation1(0);
	}
?>


</td>
</tr>
</table>
</center></div></div>
</body>
</html>
<?php include_once "includes/foot.php"; ?>
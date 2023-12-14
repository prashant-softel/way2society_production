<?php //<include_once "ses_set_common.php"; ?>
<?php

	include_once("includes/head_s.php");


include_once("classes/service_prd_reg.class.php");
$obj_service_prd_reg = new service_prd_reg($m_dbConn,$m_dbConnRoot);
?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/jsservice_prd_reg.js"></script>
    <script language="javascript" type="application/javascript">
	function go_error()
	{
		setTimeout('hide_error()',6000);	
	}
	function hide_error()
	{
		document.getElementById('error').style.display = 'none';
	}
	</script>
    
    <!--<link rel="stylesheet" href="css/ui.datepicker.css" type="text/css" media="screen" />
	<script type="text/javascript" src="javascript/jquery-1.2.6.pack.js"></script>
    <script type="text/javascript" src="javascript/jquery.clockpick.1.2.4.js"></script>
    <script type="text/javascript" src="javascript/ui.core.js"></script>
    <script type="text/javascript" src="javascript/ui.datepicker.js"></script>-->
    <script language="JavaScript" type="text/javascript" src="js/validate.js"></script> 
    <script type="text/javascript">
	$(function()
	{
		$.datepicker.setDefaults($.datepicker.regional['']);
		$(".basics").datepicker({ 
		dateFormat: "yy-mm-dd", 
		showOn: "both", 
		buttonImage: "images/calendar.gif", 
		buttonImageOnly: true 
	})});
    </script>
</head>

<?php if(isset($_POST['ShowData']) || isset($_REQUEST['msg']) || isset($_REQUEST['nul'])) { ?>
<body onLoad="go_error();">
<?php }else{ ?>
<body>
<?php } ?>
<br>
<div class="panel panel-info" id="panel" style="display:block;width:70%;margin-left:3.5%;margin-top:3%">
        <div class="panel-heading" id="pageheader">Advance Search Of Service Provider</div>

<br><br>
<?php if(!isset($_SESSION['sadmin'])){?>
<center>
<button type="button" class="btn btn-primary" onclick="window.location.href='service_prd_reg_view.php?srm'">Back to service provider list</button>
</center>
<br><br>
<?php } ?>

<center>
<form method="post" action="service_prd_export.php">
<table align="center" border="0">
<?php if(isset($_SESSION['sadmin'])){?>
<tr align="left">
	<td>Society</td>
    <td>&nbsp;:&nbsp;</td>
    <td>
    <select name="society_id" id="society_id" style="width:280px;" onChange="get_wing(this.value);">
	<?php echo $combo_society = $obj_service_prd_reg->combobox07("select society_id,society_name from society where status='Y' order by society_id desc",$_REQUEST['society_id']); ?>
    </select>
    </td>
</tr>
<?php }?>
<tr align="left">
    <td valign="top">Category</td>
    <td valign="top">&nbsp;:&nbsp;</td>
    <td>
    <div style="overflow-y:scroll;overflow-x:hidden;width:280px; height:150px; border:solid #E1E1E1 1px;">
    <?php 
    if(!isset($_REQUEST['search']))
    {
        echo $combo_cat_id = $obj_service_prd_reg->combobox11("select cat_id,cat from cat where status='Y' order by cat","cat_id[]","cat_id");
    }
    else
    {
        if($_REQUEST['cat_id']<>"")
        {
            foreach($_REQUEST['cat_id'] as $k => $v)
            {
                $cat_id0 .= $v.',';
            }
            $cat_id = substr($cat_id0,0,-1);
        }
    echo $combo_cat_id = $obj_service_prd_reg->combobox111("select cat_id,cat from cat where status='Y' order by cat","cat_id[]","cat_id",$cat_id);
    }
    ?>
    </div>
    </td>
</tr>
<tr align="left">
	<td>Search</td>
    <td>&nbsp;:&nbsp;</td>
    <td><input type="text" name="key" value="<?php echo $_REQUEST['key'];?>" size="44"></td>
</tr>
<tr><td colspan="3">&nbsp;</td></tr>
<tr><td colspan="3" align="center"><input type="submit" name="search" id="insert" value="Search" style="color: #fff;background-color: #337ab7;border-color: #2e6da4;width:30%;height:20%;margin-bottom:5px"></td></tr>
</table>
</form>

</center>
</div>
</body>
<?php include_once "includes/foot.php"; ?>
<?php include_once "ses_set_common.php"; ?>
<?php

if(isset($_SESSION['admin']))
{
	include_once("includes/header.php");
}
else if(isset($_SESSION['sadmin']))
{
	include_once("includes/header_s.php");
}
else
{
	include_once("includes/header_m.php");
}

include_once("classes/service_prd_reg_other.class.php");
$obj_service_prd_reg_other = new service_prd_reg_other($m_dbConn);
?>

<html>
<head>
<link rel="stylesheet" type="text/css" href="css/pagination.css" >
<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="js/jsservice_prd_reg.js"></script>
</head>

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

<?php if(isset($_GET['del']) || isset($_GET['add']) || isset($_GET['up'])){?>
<body onLoad="go_error();">
<?php }else{ ?>
<body>
<?php } ?>


<div id="middle">
<center><font color="#43729F" size="+1"><b>List of service provider on other society</b></font></center>
<br>

<center>
<?php if(isset($_SESSION['admin'])){?>
<a href="service_prd_reg.php?srm" style="color:#00F; text-decoration:none;"><b>Register here</b></a>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href="service_prd_reg_view.php?srm" style="color:#00F; text-decoration:none;"><b>My Provider</b></a>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?php } ?>
<a href="service_prd_reg_search.php?srm" style="color:#00F; text-decoration:none;"><b>Search here</b></a>
</center>


<?php
if(isset($_GET['del']) || isset($_GET['add']) || isset($_GET['up'])){
?>
<br>
<table align="center" border="0">
<tr>
	<td valign="top" align="center"><font color="red"><?php if(isset($_GET['del'])){echo "<b id=error>Record deleted successfully</b>";}else if(isset($_GET['add'])){echo '<b id=error>Record added successfully</b>';}else if(isset($_GET['up'])){echo '<b id=error>Record updated successfully</b>';}else{} ?></font></td>
</tr>
</table>
<?php } ?>

<center>
<table align="center" border="0">
<tr>
<td>
<?php
echo "<br>";
$str1 = $obj_service_prd_reg_other->pgnation();
echo "<br>";
echo $str = $obj_service_prd_reg_other->display1($str1);
echo "<br>";
$str1 = $obj_service_prd_reg_other->pgnation();
echo "<br>";
?>
</td>
</tr>
</table>
</center>

<br><br><br>
<br><br><br>



</body>
</html>

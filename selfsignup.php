<?php
include_once("includes/header_empty.php");
include_once("classes/selfsignup.class.php");
//include_once("classes/include/dbop.class.php");
//$m_dbConnRoot = new dbop(true);
$obj_self_sign = new selfsignup();
?>
 


	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
   <!--  <script type="text/javascript" src="lib/js/jquery.min.js"></script>
	<script type="text/javascript" src="lib/js/ajax.js"></script> -->
	<!-- <script type="text/javascript" src="js/ajax.js"></script> -->
	<script type="text/javascript" src="js/selfsignup.js"></script>
	<!--  <link href="bower_components/bootstrap-social/bootstrap-social.css" rel="stylesheet"> -->
<!-- <script type="text/javascript" src="js/validate.js"></script> -->
    <script type="text/javascript" src="js/jquery-2.0.3.min.js"></script>
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
	<Style>
body { 
  background:url(images/login_bg.jpeg) no-repeat center center fixed; 
  -webkit-background-size: cover;
  -moz-background-size: cover;
  -o-background-size: cover;
  background-size: cover;
}
</style>

<br><br>
<body>

<!-- <h2 align="center" id='top'>Self Sign Registration</h2>
 -->

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

<center>
 <div  style="display:block;background-color: #f2f2f2;border-radius: 20px;padding: 20px;width: 80%;">

<table align='center' class="form-group">
<?php
if(isset($msg))
{
	if(isset($_POST['ShowData']))
	{
?>
		<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $_POST['ShowData']; ?></b></font></td></tr>
<?php
	}
	else
	{
	?>
		<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $msg; ?></b></font></td></tr>
	<?php
	}
}
else
{
?>
		<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $_POST['ShowData']; ?></b></font></td></tr>
<?php
}
?>
<h2>Self SignUp Registration</h2>
</table>
<div style="background-color:#FFF;border-radius: 10px;line-height: 40px;" >
<!-- 	<form name="self_sign" id="self_sign" method="POST" action="process/selfsignup.process.php"  onSubmit="return val();">  -->
	<form name="classified" id="classified" method="post" action="process/selfsignup.process.php"  onSubmit="return val();">

		<!-- <form action="process/selfsignup.process.php" method="post">   -->
<table align='center' class="form-group">
		<tr><td colspan="3"></td></tr>
		<tr><td><br></td></tr>
		<tr style="line-height: 0px;">
		<td colspan="3">Please download sample file fillup the details. <a href="samplefile/Society_Data.csv" download="">Click here to Download</a></td>
	</tr>
	<tr>
		<td colspan="3" style="text-align: center;">Send fillup file on this email <a href = "mailto: info@way2society.com">info@way2society.com</a></td>
	</tr>
		<tr>
			<td><span style="font-size:0.9vw;">Society Name<?php echo $star;?></span></td>
			 <td>:&nbsp;</td>
			<td><input type="text" name="soc_name" id="soc_name" value="" class="form-control" style="height:35px; width: 14.8vw;"/></td>
		</tr>
		<tr>
			<td><span style="font-size:0.9vw;">Name<?php echo $star;?></span></td>
			 <td>:&nbsp;</td>
			<td><input type="text" name="name" id="name" value="" class="form-control"  style="height:35px; width: 14.8vw;"/></td>
		</tr>
		<tr>
			<td><span style="font-size:0.9vw;">Contact No.<?php echo $star;?></span></td>
			 <td>:&nbsp;</td>
			<td><input type="text" name="number" id="number" value="" class="form-control" style="height:35px; width: 14.8vw;"  min="0" maxlength="10" oninput=this.value=this.value.replace(/[^0-9]/g,''); /></td>
		</tr>

		<tr>
			<td><span style="font-size:0.9vw;">Email<?php echo $star;?></span></td>
			 <td>:&nbsp;</td>
			<td><input type="email" name="email" id="email" value="" class="form-control" style="height:35px; width: 14.8vw;"/></td>
		</tr>
		<tr>
			<td><span style="font-size:0.9vw;">Designation<?php echo $star;?></span></td>
			 <td>:&nbsp;</td>
			<td><input type="text" name="desg" id="desg" value="" class="form-control" style="height:35px; width: 14.8vw;"/></td>
		</tr>
		
		<tr>
			<td><span style="font-size:0.9vw;">No. Of Units<?php echo $star;?></span></td>
			 <td>:&nbsp;</td>
			<td><input type="text" name="no_of_unit" id="no_of_unit" value="" class="form-control" style="height:35px; width: 14.8vw;" min="0"  oninput=this.value=this.value.replace(/[^0-9]/g,''); /></td>
		</tr>
		<tr>
			<td><span style="font-size:0.9vw;">Soceity Addrass<?php echo $star;?></span></td>
			 <td>:&nbsp;</td>
			<td>
				<!-- <input type="text" name="soc_add" id="soc_add" value="" class="form-control" style="height:35px; width: 14.8vw;"/> -->
				<textarea rows="5"  name="soc_add" id="soc_add" class="form-control" style="width: 202px;"></textarea>
			</td>
		</tr>
		
		<tr><td colspan="3"><br></td></tr>
		<tr>
			<td colspan="3" align="center"><input type="hidden" name="id" id="id">
				<input type="submit" name="insert" id="insert" value="Submit"  class="btn btn-primary" style=" width: 50%;height:35px;font-weight:bold; background-color: #337ab7;border-color: #2e6da4;color: white;"/>
				<!-- <input type="submit" name="insert" id="insert" value="Submit" class="btn btn-primary"  style=" width: 50%;height:35px;font-weight:bold; background-color: #337ab7;border-color: #2e6da4;color: white;"> --></td>
		</tr>
	<tr><td><br></td></tr>
	
	
</table>
</div>

</form>


<table align="center">
<tr>
<td>
<?php
// echo "<br>";
// $str1 = $obj_self_sign->pgnation();
// echo "<br>";
// echo $str = $obj_self_sign->display1($str1);
// echo "<br>";
// $str1 = $obj_self_sign->pgnation();
// echo "<br>";
?>
</td>
</tr>
</table>
</div>
</center>

</body>
</html>

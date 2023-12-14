<html>
<head>
<meta charset="utf-8">
<title>W2S - OTP Setting</title>
	<style>
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.switch input {display:none;}

.slider1 {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ca2222;
  -webkit-transition: .4s;
  transition: .4s;
	height: 35px !important;
    width: 80px !important;
}

.slider1:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider1 {
  background-color: #2ab934;
}

input:focus + .slider1 {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider1:before {
  -webkit-transform: translateX(55px);
  -ms-transform: translateX(55px);
  transform: translateX(55px);
}

/*------ ADDED CSS ---------*/
.on
{
  display: none;
}

.on, .off
{
  color: white;
  position: absolute;
  transform: translate(-50%,-50%);
  top: 50%;
  left: 50%;
  font-size: 10px;
  font-family: Verdana, sans-serif;
}

input:checked+ .slider1 .on
{display: block;}

input:checked + .slider1 .off
{display: none;}

/*--------- END --------*/

/* Rounded sliders */
.slider1.round {
  border-radius: 34px;
}

.slider1.round:before {
  border-radius: 50%;}
</style>
</head>

<?php
include_once("includes/head_s.php"); 
include_once("classes/OTP_Setting.class.php");
include_once("classes/include/dbop.class.php");


$smConn = new dbop(false,false,true,false);
$dbConn = new dbop();

$ObjOTPSetting = new OTP_Setting($dbConn,$smConn);

	if(isset($_POST['submit']))
	{
	$rep="1";
	$new="1";
	$exp="1";
	//to run PHP script on submit
		if(!empty($_POST['check_list']))
		{
		foreach($_POST['check_list'] as $selected)
		{
			if($selected=="2")
			{
				$rep="0";
			}
			if($selected=="1")
			{
				$new ="0";
			}
			if($selected=="3")
			{
				$exp="0";
			}
		}
		}
	
	$checkedstatus=$ObjOTPSetting->updaterecord($rep,$new,$exp);
	
	}




$optstatus=$ObjOTPSetting->otpstatus();
$repcheck=$optstatus[0]['OTP_Status_Rep'];
$newcheck=$optstatus[0]['OTP_Status_New'];
$expcheck=$optstatus[0]['OTP_Status_Exp'];

?>


	

	<body>
<div class="panel panel-info" style="margin-top:3%;margin-left:3.5%; border:none;width:65%;">
 
    <div class="panel-heading text-center" style="font-size:20px">
		OTP Setting
		</div>
	<br><br>
		<center>
		<form action="#" method="post">

			<table>
				<tr>
					<td>
<label style="font-size: 15px;margin-top: 10px">For Repeating Visitor : </label>
					</td>
					
					<td style="padding-left: 5px;">
<label class="switch"><input type="checkbox" id="repeating" name="check_list[]" value="2"><div class="slider1 round"><!--ADDED HTML --><span class="on">ON</span><span class="off">OFF</span><!--END--></div></label>
					</td>
				</tr>
				<tr></tr>
				<tr>
					<td>
<label style="font-size: 15px;margin-top: 10px">For New Visitor : </label>
					</td>
					<td>
<label class="switch"><input type="checkbox" id="new" name="check_list[]" value="1"><div class="slider1 round"><!--ADDED HTML --><span class="on">ON</span><span class="off">OFF</span><!--END--></div></label>
			</td>
				</tr>
				<tr></tr>
				<tr>
					<td>
				<label style="font-size: 15px;margin-top: 10px">For Expected Visitor : </label>
					</td>
					
					<td style="padding-left: 5px;">
<label class="switch"><input type="checkbox" id="expected" name="check_list[]" value="3"><div class="slider1 round"><!--ADDED HTML --><span class="on">ON</span><span class="off">OFF</span><!--END--></div></label>
					</td>
				</tr>
			</table>
			<br>
<input type="submit" name="submit" value="Submit" class="btn btn-primary"/>
		
</form>
		
		</center>
	<br>
		</div>
				
		
	</body>
	<script>
<?php if($repcheck == 0)
{ ?>
	document.getElementById("repeating").checked=true;
<?php }
		if($newcheck==0)
		{
?>
	document.getElementById("new").checked=true;
<?php 
		
		}
		if($expcheck==0)
		{
			?>
		document.getElementById("expected").checked=true;
		<?php
		}
	?>
</script>
<?php include_once "includes/foot.php"; ?>
</html>

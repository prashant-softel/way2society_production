<?php //include_once("includes/head_s.php");
    error_reporting(0);
    include_once("includes/header_empty.php");
	include_once("classes/initialize.class.php");
	include_once("classes/utility.class.php");
	
	include_once("Facebook/fbmain.php");
	
	//include_once("classes/defaults.class.php");
	include_once("classes/include/dbop.class.php");
	$m_dbConnRoot = new dbop(true);
	$obj_initialize = new initialize($m_dbConnRoot);
	$msg = '';
	
	$userRegEmail = "";
	$userRegName = "";
	$userRegCode = "";
	$bVaildLink = false;
	$URL = "";
	if(isset($_REQUEST['URL']) || isset($_REQUEST['url']))
	{
		$URL = $_REQUEST['URL'];	
	}

	$FBCallBackURL = "";

	/*********************************************************/
	//Create call back URL for FB Login

	$FBCallBackURL = FB_BASE_APP_URL . "newuseractivation.php?";
	$FBCallBackURL = "https://way2society.com/newuseractivation.php?";
	//$FBCallBackURL = "http://localhost/beta_aws_4/newuseractivation.php?";
	if(isset($_REQUEST['reg']))
	{
		$FBCallBackURL .= 'reg';
	}

	if(isset($_REQUEST['c']))
	{
		$FBCallBackURL .= '&c='.$_REQUEST['c'];
	}
	
	if(isset($_REQUEST['url']))
	{
		$FBCallBackURL .= '&url='.$_REQUEST['url'];
	}
	if(isset($_REQUEST['u']))
	{
		$FBCallBackURL .= '&u='.$_REQUEST['u'];
	}
	if(isset($_REQUEST['n']))
	{
		$FBCallBackURL .= '&n='.$_REQUEST['n'];
	}
	if(isset($_REQUEST['tkn']))
	{
		$FBCallBackURL .= '&tkn='.$_REQUEST['tkn'];
	}
	echo "url:".$FBCallBackURL;
	setLoginURL($FBCallBackURL);

	/*********************************************************/

	//echo "<h3>Login URL : </h3>" . $loginUrl;

	$obj_utility = new utility($m_dbConnRoot);	
	if(isset($_REQUEST['reg']))
	{
		$bCheckEmailIdAlreadyExists = $obj_initialize->bEmailIdAlreadyExists($_REQUEST['u']);
		if($bCheckEmailIdAlreadyExists == true)
		{
			$encryptedEmail =$obj_utility->encryptData($_REQUEST['u']);
			if(isset($_REQUEST['url']))
			{
				
			?>
            <script>
				 window.location.href = "http://way2society.com/login.php?u=<?php echo $encryptedEmail ?>&mCode=<?php echo $_REQUEST['c'] ?>&url=<?php echo $_REQUEST['url']?>";
			</script>
	<?php  }
            else
            {
				
             ?>  
              <script>
				window.location.href = "http://way2society.com/login.php?u=<?php echo $encryptedEmail ?>&mCode=<?php echo $_REQUEST['c'] ?>";
			</script>
            
		<?php } 
		}

        	
		if(isset($_REQUEST['u']))
		{
			if(isset($_REQUEST['tkn']))
			{
				$userRegTkn = $obj_utility->decryptData($_REQUEST['tkn']);
				$userRegEmail = $_REQUEST['u'];
				$_REQUEST['email'] = $userRegEmail;
				if($userRegTkn == $userRegEmail)
				{
					$bVaildLink = true;
				}
			}
		}
		
		if(isset($_REQUEST['n']))
		{
			$userRegName = $_REQUEST['n'];
			if(!isset($_REQUEST['name']))
			{
				$_REQUEST['name'] = $userRegName;
			}
		}
	}
	
		if(isset($_REQUEST['c']))
		{
			$bVaildLink = true;
		}
	if(isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'verify')
	{
		if(isset($_REQUEST['c']) && $_REQUEST['c'] <> "")
		{
			if($bVaildLink == true)
			{
				$result = $obj_initialize->addUser($_REQUEST['name'], $_REQUEST['email'], $_REQUEST['pass'], '', true, $_REQUEST['c'],$_REQUEST['mob']);
			}
			else
			{
				$result = 0;
				$msg = 'We are unable to activate your account. Kindly re-click on the link in the email and try again.';
			}
		}
		else
		{
			if(isset($_REQUEST['fb_id']) && $_REQUEST['fb_id'] == 0 )
			{
				$result = $obj_initialize->addUser($_REQUEST['name'], $_REQUEST['email'], $_REQUEST['pass'], '', true,"",$_REQUEST['mob']);
			}
			else if($_REQUEST['fb_id'] > 0 )
			{
				$result = $obj_initialize->addUser($_REQUEST['name'], $_REQUEST['email'], '',$_REQUEST['fb_id'], true,"",$_REQUEST['mob']);
			}
		}
		
		if($result > 0)
		{
			goto sendEmail;
			//goto mainPage;
		}
		else if($result == -1)
		{
			$msg = '<font size="35" >E-Mail ID [' . $_REQUEST['email'] . '] Is Already Registered.</font><br><input type="button" onClick="ClosePage()" style="font-size:40px;width:200px;height:100px" value= "OK"/><br><a href="forgotpassword.php" style="font-size:40px">Recover Password</a>';
						
		}
		
	}
	else if(isset($_GET['code']) && $_GET['code'] <> '')
	{
		
		$fb_details = $UserDetails;	
		 echo "<!-- fb_details:\n".sizeof($fb_details).print_r($fb_details, TRUE)."\n-->\n\n";
	
		if($fb_details['fbid']<>"")
		{
			//Add the user to DB
			if(isset($_REQUEST['c']))
			{
				$bVaildLink = true;
				if($bVaildLink == true)
				{
					//goto FB_Details;
					//$result = $obj_initialize->addUser($fb_details['name'], $fb_details['email'], '', $fb_details['fbid'], true, $_REQUEST['c'],"0");
				}
			}
			else
			{
				//$result = $obj_initialize->addUser($fb_details['name'], $fb_details['email'], '', $fb_details['fbid'], true,"","0");
			}
		}
		 }
	$star = "<font color='#FF0000'>*</font>";
	
	
	sendEmail:
{
	if($result > 0)
	{
		$data = $_REQUEST;
		$res = $obj_initialize->fetchLoginDetails($result);
		$result2 = $obj_initialize->sendEmail($res,false,$data);
		if($result == true)
		{
				//$msg = 'Your request has been recorded successfully.<br>We will get back to you soon.';
		}
		else if($result == false)
		{
				//$msg = 'Your request not recorded.Please try again.';
		}	
	}
//	echo "<pre>";
//	print_r($result);
//	echo "</pre>";
}
//
	
?>
<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">-->
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>New User registration</title>
 <link href="bower_components/bootstrap-social/bootstrap-social.css" rel="stylesheet">
<script type="text/javascript" src="js/validate.js"></script>
<script type="text/javascript" src="js/jquery-2.0.3.min.js"></script>
<script  language="javascript" type="text/javascript">
function ClosePage()
				   {
				   	alert("closing d");
					 //var myWindow = window.open("", "_self");
  //myWindow.document.write("");
  //setTimeout (function() {myWindow.close();},1000);
  var win = window.open("","_self"); /* url = "" or "about:blank"; target="_self" */

win.close();
					  //open(location, '_self').close();
					 // window.close();
					 //var customWindow = window.open('', '_blank', '');
    //customWindow.close();
				   }
function go_error()
{
	setTimeout('hide_error()',10000);	
}
function hide_error()
{
	document.getElementById('error').style.display = 'none';	
}
	
function varifyForm()
{
	var fb_id = trim(document.getElementById("fb_id").value);
	var name = trim(document.getElementById("name").value);
	var email = trim(document.getElementById("email").value);
	var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
	//var mob = trim(document.getElementById("mob").value);
	//var society_name = trim(document.getElementById("society_name").value);
	//var  unit_no = trim(document.getElementById("unit").value);
	
	
	if(name == "")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter name";
		document.getElementById("name").focus();
		
		go_error();
		return false;
	}
	else if(email == "")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter email id";
		document.getElementById("email").focus();
		
		go_error();
		return false;
	}
	else if(email != "" && reg.test(email) == false)
	{
		document.getElementById('error').style.display = '';
		document.getElementById('error').innerHTML = 'Invalid email id';	
		document.getElementById('email').focus();
			
		go_error();
		return false;	
	}
	/*else if(mob == "")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter mobile number";
		document.getElementById("mob").focus();
		
		go_error();
		return false;
	}*/
	
	if(fb_id == "" ||  fb_id == "0")
	{
		var  pass1 = trim(document.getElementById("pass").value);
		var  pass2 = trim(document.getElementById("repass").value);
	
		if(pass1 == "")
		{
			document.getElementById('error').style.display = '';	
			document.getElementById("error").innerHTML = "Enter password";
			document.getElementById("pass").focus();
			
			go_error();
			return false;
		}
		else if(pass2 == "")
		{
			document.getElementById('error').style.display = '';	
			document.getElementById("error").innerHTML = "Enter password";
			document.getElementById("repass").focus();
			
			go_error();
			return false;
		}
		else if(pass1 != pass2) 
		 {
				document.getElementById('error').style.display = '';	
				document.getElementById("error").innerHTML = "Password does not matched";
						
				go_error();
				return false;
		 }
	}
	//document.forms['verify_code'].submit();
	if(document.getElementById('agree').checked) 
	{ 
		return true; 
	} 
	else 
	{ 
		alert('Please read and accept the terms and conditions to create your account.'); 
		return false; 
	}
	//document.getElementById("verify_code").submit();
	/*if(society_name == "")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter society name";
		document.getElementById("name").focus();
		
		go_error();
		return false;
	}
	else if(unit_no == "") 
	 {
			document.getElementById('error').style.display = '';	
			document.getElementById("error").innerHTML = "Enter Unit/Flat/Shop No";
					
			go_error();
			return false;
	 }
	*/
	
   function LTrim( value )
	{
	var re = /\s*((\S+\s*)*)/;
	return value.replace(re, "$1");
	}
	function RTrim( value )
	{
	var re = /((\s*\S+)*)\s*/;
	return value.replace(re, "$1");
	}
	function trim( value )
	{
	return LTrim(RTrim(value));
	}
	//this.form.submit();
	//document.getElementById("verify_code").submit();
}

function hideFormFields(ClassName)
{
	var fields = document.getElementsByClassName(ClassName), i;

	for (i = 0; i < fields.length; i += 1)
	{
		//closefd[i].style.visibility = 'hidden';
		fields[i].style.display = 'none';
	} 	
}


function activateButton(element)
 {

   if(element.checked)
   {
		document.getElementById("submit").disabled = false;
   }
   else 
   {
		document.getElementById("submit").disabled = true;
   }

 }

</script>
<Style>
body { 
  -moz-background-size: cover;
  -o-background-size: cover;
  background-size: cover;
}
</style>
</head>
<body>
	<center>
    	<h2 style="display:none;">Create Account</h2>
    	  <br /><br />
		
        <br />
        <!--<input type="button" value="I Don't have Facebook Account" onclick="document.getElementById('no_fb').style.display='block'" />-->
        <a href="#" onClick="document.getElementById('no_fb').style.display='block';document.getElementById('reg_link').style.display='none';"  id="reg_link" style="display:none;">Don't Have A Facebook Account ?</a>
        <div id="no_fb" style="display:block;background-color: #f2f2f2;border-radius: 10px;padding: 10px;width: 90%;">
            <h2>Registration</h2>
            <br />
           <div style="background-color:#FFF;border-radius: 10px;line-height: 100px;" >
            <div id="msg" style="color:#FF0000;font-weight:bold;padding:10px;"><?php echo $msg; ?></div>
            <form name="verify_code" id="verify_code"  method="post" action="<?php  echo $FBCallBackURL; ?>"  onSubmit="return varifyForm();" style="">
             <fieldset>
             	<input type="button" onClick="ClosePage()" style="font-size:40px;width:200px;height:100px" value= "OK"/>
                <input type="hidden" name="mode" value="verify"  />
                <input type="hidden" name="fb_id"  id="fb_id" value="<?php if(sizeof($fb_details) > 0 && $fb_details['fbid'] <> ''){ echo $fb_details['fbid'];}else{ echo '0';}?>" />
              <?php  echo "<!-- fb_details:\n".sizeof($fb_details).print_r($fb_details, TRUE)."\n-->\n\n";?>
                 <div  id="fb_btn">
                    
                     <div class="login-panel panel panel-default" style="margin-top:2%; width:50vw;"  id="fbbtn">
                            <div class="panel-body" style="width:40vw;" >
                            <?php
                                    if($_SERVER['HTTP_HOST'] == 'localhost')
                                    {
                                    ?>
                                        <a href="javascript:void(0);" class="btn btn-block btn-social btn-facebook" onClick="alert('Facebook Connectivity Not Available On Localhost');"><i class="fa fa-facebook"></i><span style="font-size:2vw;"><b> Sign up using Facebook</b></span></a>
                            
                                    <?php 
                                    }
                                    else
                                    {
                                    ?>
                                        <!--<a href="<?php echo $loginUrl?>" id="fb_link"><img src="fb/fb_connect.jpg" alt="" /></a>-->
                                        <a href="<?php echo $loginUrl?>" id="fb_link"  class="btn btn-block btn-social btn-facebook" ><i class="fa fa-facebook"></i><span style="font-size:2vw;"><b> Sign up using Facebook</b></span></a>
                                    <?php 
                                    }
                            ?>
                         </div>		
                    </div>
                    <div class="login-panel panel panel-default" style="margin-top:-2%;font-size:2vw;margin-bottom:-3%;width:25vw;text-align:center;border:none;box-shadow: 0px 0px 0px rgba(0, 0, 0, 3.0);" id="orbtn">OR</div>
              </div>
              <center>
                <table class="form-group" style="">
                	<tr height="30"><td colspan="3" align="center"><font color="red" style="size:11px;"><b id="error"></b></font></td></tr>
                    <tr><td colspan="3" align="justify"  id="info"></td></tr>
                    <tr class="prePopulated" align="left">
                        <td  valign="left" style="font-size:3vw;">Name<?php echo $star;?></td>
                        <td align="center">:&nbsp;</td>
                        <td ><input type="text" class="form-control" name="name" id="name" value="<?php echo $_REQUEST['name']; ?>"  style="height:50px; width: 40vw;font-size: 3vw"/></td>
                    </tr>
                    <tr class="prePopulated" align="left">
                        <td valign="left" style="font-size:3vw;">E-Mail<?php echo $star;?></td>
                        <td align="center">:&nbsp;</td>
                        <td><input type="text" class="form-control"  name="email" id="email" value="<?php  echo $_REQUEST['email']; ?>" style="height:50px; width: 40vw;;font-size: 3vw" /></td>
                    </tr>
                    <!--<tr>
                        <td>Security Code<?php //echo $star;?></td><td>:&nbsp;</td>
                        <td><input type="text" name="code" id="code" /></td>
                    </tr>-->
                    
                   <!-- <tr>
                        <td><span style="font-size:1vw;">Mobile Number<?php echo $star;?></span></td>
                        <td>:&nbsp;</td>
                        <td><input    name="mob" id="mob" type="text" class="form-control"  maxlength="10"  pattern="[0-9]{10}" value="<?php //echo $_REQUEST['mob']; ?>"  title="Enter your 10 digit mobile number." onKeyPress='return blockNonNumbers(this, event, false, false);' style="height:35px; width: 14.8vw;"></td>
                    </tr>-->
                     
                     <tr class="normalUser" align="left">
                        <td valign="left" style="font-size:3vw;">Password<?php echo $star;?></td>
                        <td align="left">:&nbsp;</td>
                        <td><input type="password" class="form-control"  name="pass" id="pass" value="<?php echo $_REQUEST['pass']; ?>"  style="height:50px; width: 40vw;;font-size: 3vw"/></td>
                    </tr>
                     <tr class="normalUser" align="left">
                        <td valign="left" style="font-size:3vw;">Confirm Password<?php echo $star;?></td>
                        <td align="left">:&nbsp;</td>
                        <td><input type="password" class="form-control" name="repass" id="repass" value="<?php echo $_REQUEST['repass']; ?>" style="height:50px; width: 40vw;;font-size: 3vw"/></td>
                    </tr>
                    <tr class="normalUser" align="left">
                        <td valign="left" style="font-size:3vw;">Access Code<?php echo $star;?></td>
                        <td align="left">:&nbsp;</td>
                        <td><input type="text" name="c" id="c"  value="" style="height:50px; width: 40vw;;font-size: 3vw"/></td>
                    </tr>
                    
                     <!-- <tr>
                    	<td><span style="font-size:1vw;">Society Name<?php //echo $star;?></span> </td>
                    	<td>&nbsp; : &nbsp;</td>
                    	<td><input type="text" class="form-control"  name="society_name" id="society_name" value="<?php //echo $_REQUEST['society_name'];?>" style="height:35px; width: 14.8vw;"/></td>
                    </tr>
                    
                    <tr>
                            <td><span style="font-size:1vw;">Wing Name<?php //echo $star;?></span> </td>
                            <td>&nbsp; : &nbsp;</td>
                            <td><input type="text" class="form-control"  name="wing" id="wing" value="<?php //echo $_REQUEST['wing'];?>" style="height:35px; width: 14.8vw;"/></td>
                    </tr>
                    
                    
                    <tr>
                            <td><span style="font-size:1vw;">Unit/Flat/Shop No<?php //echo $star;?></span> </td>
                            <td>&nbsp; : &nbsp;</td>
                            <td><input type="text"  class="form-control" name="unit" id="unit" value="<?php //echo $_REQUEST['unit'];?>" style="height:35px; width: 14.8vw;"/></td>
                    </tr>-->
                    
                    <tr>
                        <td colspan="3" align="center" style="padding:0px;">
                        <p style="float: none;clear: both;vertical-align: middle; border: 1px solid rgb(204, 204, 204)">
                            <input style="float:none; vertical-align: middle;margin-bottom: 5px;transform: scale(3);" type="checkbox" id="agree"  onchange="activateButton(this);"/>
                            <label style="display:inline;font-size:3vw; float:none" for="agree">
                              &nbsp;&nbsp;I agree with the <a href="http://way2society.com/?page_id=5640" 
  target="popup" 
  onclick="window.open('https://way2society.com/?page_id=5640','popup','width=600,height=600,scrollbars=yes'); return false;">terms and conditions</a>.
                            </label>
                        </p>
                        
                        </td>
                    </tr>
                    <?php
                    	if(isset($_REQUEST['c']) && $_REQUEST['c'] <> '')
                    	{
                    		?>
                    			 <!-- <input type="hidden" name="c" value="<?php //echo $_REQUEST['c']; ?>  /> -->
                    		<?php
                    	}
                    ?>
                    <tr><td></td></tr>
                    <tr>
                        <td colspan="4" align="center" style="padding:5px;"><input type="submit" value="Activate"   id="submit" class="btn btn-primary" onclick="varifyForm();"  style=" width: 70%;height:60px;font-weight:bold;font-size:3vw; /></td>
                    </tr>
                </table>
                
                </fieldset>
                 <div style="padding:10px;font-size:2vw;">
                  <p align="left" style="line-height: 40px;font-size:1.5vw;margin-left: 10px"><u>NOTE</u>
                 <br />1. Your society must be registered with "Way2Society.com".
                 <br />2. After your login is created, you will need to enter the code provided by your society to access your account.
                 </p>
             </div>
             </form>
             </div>
         </div>
      </center>
 </div>  
 </body>
</html>
<?php
	if(isset($_REQUEST['reg']) || (isset($_GET['code']) && $_GET['code'] <> ''))
	{
		?>
			<script>
				document.getElementById('no_fb').style.display='block';
				document.getElementById('reg_link').style.display='none';
				if(document.getElementById('email').value != ""){document.getElementById('email').readOnly = true;}
				$("#orbtn").insertBefore("#fbbtn");
				$("#fb_btn").insertAfter(".form-group");
				
				if(document.getElementById('email').value != "")
				{
					hideFormFields('prePopulated');
					document.getElementById("pass").focus();
				}
			
				
				//hideFormFields('prePopulated');
				<?php
				if(isset($_REQUEST['reg']))
				{
					?>
					//document.getElementById("info").innerHTML = "Hi <b><?php echo $_REQUEST['name']?>" + "</b>,Just enter Password and  confirm it, you will be connected using your email id  <b><?php echo $_REQUEST['email']?> </b><br/>";
					document.getElementById("info").innerHTML = "Hi <b><?php echo $_REQUEST['name']?>" + "</b>, Welcome to way2society.com ! <br/>You are a member of our registered society. In order to activate your account, please enter your new password  and start<br/>  taking benifits of services offered by us. Your registered email id  <b><?php echo $_REQUEST['email']?> </b> would be your user id.<br/>";
			<?php } ?>
            </script>
        <?php
	}

if(isset($_GET['code']) && $_GET['code'] <> '' && sizeof($fb_details) > 0)
	{?>
		 <script>
		 		//document.getElementById('fb_id').value = '<?php echo $fb_details['fbid']; ?>';
				document.getElementById('name').value = "<?php echo $fb_details['name']; ?>";
				//document.getElementById('name').readOnly = true;
				hideFormFields('normalUser');
			</script>
			<?php
		if($fb_details['email'] <> "")
		{?>
         <script>
		 		document.getElementById('email').value = "<?php echo $fb_details['email']; ?>";
				document.getElementById('email').readOnly = true;
		</script>
    <?php    
	}?>
	 <script>

				document.getElementById("submit").click();</script>
	<?php }



/*mainPage:
{*/
	if($result > 0)
	{
	//$initializeURL = "initialize.php?imp";
	if($URL <> "")
	{
		//$initializeURL .= "&".$URL;
	}
	?>                	
		<script>
			
				   /*window.location.href = "<?php //echo $initializeURL; ?>"*/
		<?php if($result > 0)
				{
					?>
					document.getElementById("msg").innerHTML = "<font color='#00FF00' size='35'>Your login is now created. <br>Please sign in to access your account.</font><input type='button' onClick='ClosePage()' style='font-size:40px' value= 'OK'/>";
				<?php 
				}
				else
				{
					?>
document.getElementById("msg").innerHTML = "<font color='#00FF00' size='35'>We have received your request. <br>You will receive an account activation email shortly.</font><input type='button' onClick='ClosePage()' style='font-size:40px' value= 'OK'/>";
			<?php	
		}
				?>
				    //window.location.href = "initialize.php?tknexsd";
					//setTimeout(function(){ open(location, '_self').close(); }, 3000);
				   
					document.getElementById("reg_link").style.display = "none";
					document.getElementById("fb_link").style.display = "none";
					go_error();
        </script>
	<?php
	}
/*}*/
 if(isset($_GET['code']) && $_GET['code'] <> '')
{?>
		 <script>document.getElementById("fb_btn").style.display = "none";</script>
 <?php } ?>        
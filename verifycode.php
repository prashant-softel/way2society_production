<?php include_once("includes/header_empty.php");//include_once("includes/head_s.php");
	include_once('classes/include/check_session.php');
	include_once("classes/initialize.class.php");
	include_once("classes/defaults.class.php");
	include_once("classes/include/dbop.class.php");
	
	//include_once("classes/include/dbop.class.php");
	//$m_dbConnRoot = new dbop(true);
	$obj_initialize = new initialize($m_dbConnRoot);
	
	$msg = '';
	if(isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'verify')
	{
		if(!isset($_REQUEST['code']) || $_REQUEST['code'] == '')
		{
			$msg = 'Please Enter Your Activation Code';
		}
		else
		{
			$result = $obj_initialize->verifyCode($_REQUEST['code']);
			if($result == '')
			{
				$msg = 'Invalid Activation Code Entered. Please Re-Enter The Activation Code';
			}
			else if($result[0]['status'] == 2 || $result[0]['status'] == 3)
			{
				$msg = 'Activation Code Already In Use.';
			}
			else
			{
				$obj_initialize->setLoginIDToMap($result[0]['id'], 2);
				
				$mapDetails = $obj_initialize->getMapDetails($result[0]['id']);
		
				if($mapDetails <> '')
				{
					$_SESSION['current_mapping'] = $_REQUEST['mapid'];
					$obj_initialize->setCurrentMapping($_REQUEST['mapid']);
					
					$dbName = $mapDetails[0]['dbname'];
					$_SESSION['dbname'] = $dbName;
					
					$society_id = $mapDetails[0]['society_id'];
					$_SESSION['society_id'] = $society_id;
					
					$role = $mapDetails[0]['role'];
					$_SESSION['role'] = $role;
					
					$unit_id = $mapDetails[0]['unit_id'];
					$_SESSION['unit_id'] = $unit_id;
					
					$_SESSION['desc'] = $mapDetails[0]['desc'];
					
					$obj_initialize->setProfile($mapDetails[0]['profile']);
					
					?>
						<script>window.location.href = "initialize.php?set";</script>
					<?php
				}
			}
		}
	}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Activate Your Account</title>
</head>
<body>

	<center>
    <br />
    <br />
	 <table align="center" cellspacing="10">
        	<h3><a href='society_details.php'>Don`t have activation code, Enter society details</a></h3>
        </table>
        <br />
        OR
    	<br />
    	<br />
    	
    	<div class="panel panel-info" id="panel"  style="width:40%;border-radius:10px;margin-top: 0%">
		<div class="panel-heading" id="pageheader" style="font-size:16px;"><b>Link Your Society/Flat</b></div>
        
        <div id="msg" style="color:#FF0000;font-weight:bold;margin-top: 5%"><?php echo $msg; ?></div>

    	<form name="verify_code"  method="post" action="">
    		<table  style="margin-top: 5%">
    			<tr style="margin-top: 5%"><td><span style="vertical-align: middle;">Enter Your Activation Code </span></td><td>:</td><td> <input type="text" id="code" name="code" style="margin-top: 5%;height: 30px" /></td>
    			</tr>
    		</table>
    		<table style="margin-top: 5%;margin-bottom: 5%">
    			<tr><td colspan="3">
		            <input type="hidden" name="mode" value="verify" />
		            </td>
		        </tr>
		         <tr><td colspan="3">
		        	<input type="submit" class="btn btn-primary" value="Verify Activation Code" style="margin-bottom: 5%" />
		        </td>
			    </tr>
			</table>
		</form>
    </div>
    </center>
</body>
</html>
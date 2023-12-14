<?php  include_once("includes/header_empty.php");
	//include_once("includes/head_s.php");
	include_once('classes/include/check_session.php');
	include_once("classes/initialize.class.php");
	include_once("classes/defaults.class.php");
	 include_once("classes/dbconst.class.php");
	//include_once("classes/include/dbop.class.php");
	
	//include_once("classes/include/dbop.class.php");
	//$m_dbConnRoot = new dbop(true);
	$obj_initialize = new initialize($m_dbConnRoot);
	
	//print_r($_SESSION);
	
	if(!isset($_SESSION['login_id']) || $_SESSION['login_id'] == '' || $_SESSION['login_id'] == 0)
	{
		?>
			<script type="text/javascript">
				window.location.href = "login.php";
			</script>
		<?php
		exit();
	}
	
	$mapCnt = $obj_initialize->getMapCount($_SESSION['login_id']);
	
	//echo '<br>Login ID : ' . $_SESSION['login_id'];
	//echo '<br>Authority : ' . $_SESSION['authority'];
	
	//$obj_default = new defaults($m_dbConn);

	if(isset($_REQUEST['mode']) && $_REQUEST['mode'] == "set")
	{	echo "1 :";			
		$mapDetails = $obj_initialize->getMapDetails($_REQUEST['mapid']);
		if($mapDetails <> '')
		{
			echo "2 :";
			$_SESSION['current_mapping'] = $_REQUEST['mapid'];
			$obj_initialize->setCurrentMapping($_REQUEST['mapid']);
			echo "3 :";
			$dbName = $mapDetails[0]['dbname'];
			$_SESSION['dbname'] = $dbName;
			echo "4 :";
			$society_id = $mapDetails[0]['society_id'];
			$_SESSION['society_id'] = $society_id;
			echo "5 :";
			$role = $mapDetails[0]['role'];
			$_SESSION['role'] = $role;
			echo "6 :";
			$view = $mapDetails[0]['view'];
			$_SESSION["View"] = strtoupper($view);
			echo "7 :";
			$unit_id = $mapDetails[0]['unit_id'];
			$_SESSION["unit_id"] = $unit_id;
			echo "8 :";
			$_SESSION['desc'] = $mapDetails[0]['desc'];
			$_SESSION['society_client_id'] = $mapDetails[0]['client_id'];
			echo "9 :";
			$sqlFeatureList = "SELECT * FROM `clientwise_features`  where `CLIENT_ID` = '" .$_SESSION['society_client_id']. "'";
			$resultFeatureList = $m_dbConnRoot->select($sqlFeatureList);
			
			$_SESSION['feature'][CLIENT_FEATURE_EXPORT_MODULE] = 0;
			$_SESSION['feature'][CLIENT_FEATURE_SMS_MODULE] = 0;
			echo "10 :";
			if($resultFeatureList <> '')
			{
				$_SESSION['feature'][CLIENT_FEATURE_EXPORT_MODULE] = $resultFeatureList[0]['CLIENT_FEATURE_EXPORT_MODULE'];
				$_SESSION['feature'][CLIENT_FEATURE_SMS_MODULE] = $resultFeatureList[0]['CLIENT_FEATURE_SMS_MODULE'];
			}
			echo "11 :";
			//$_SESSION['owner_id'] = $obj_default->getMemberID($_SESSION['society_id'],$_SESSION['unit_id']);
			
			$obj_initialize->setProfile($mapDetails[0]['profile']);
			echo "12 :";
			$obj_initialize->getModuleAccess();
			echo "13 :";
			?>
            	<script>
					localStorage.setItem('login', "<?php echo $_SESSION['login_id']; ?>");
					localStorage.setItem('client_id', "<?php echo $_SESSION['society_client_id']; ?>");
					localStorage.setItem('dbname', "<?php echo $_SESSION['dbname']; ?>");
					//window.location.href = "initialize.php?set&url=" + "<?php //echo $_REQUEST['url']?>";
				</script>
            <?php
			if(isset($_REQUEST['url']))
			{
				echo "14 :";
				if($_SESSION['society_id']==127 || $_SESSION['society_id']==59)
				{?>
					<script>window.location.href = "initialize_test.php?set&url=" + "<?php echo $_REQUEST['url']?>";</script>
				<?php }
				else
				{
				?>
            	<script>window.location.href = "initialize.php?set&url=" + "<?php echo $_REQUEST['url']?>";</script>
<?php }}
			else
			{
			echo "15 :";	?>
            <script>window.location.href = "initialize.php?set";</script>
<?php }
		}
		
	}
	else if(isset($_REQUEST['set']))
	{ echo "16 :";
		$obj_default = new defaults($m_dbConn,$m_dbConnRoot);
		$obj_default->getDefaults($_SESSION['society_id'], true);
		$obj_initialize->saveLoginDetails();	
		$_SESSION['owner_id'] = $obj_default->getMemberID($_SESSION['society_id'],$_SESSION['unit_id']);
		if($_REQUEST["View"] == "ADMIN")
		{
			echo "17 :";
			$_SESSION["View"] = "ADMIN";
		}
		else
		{
			$_SESSION["View"] = "MEMBER";			
		}
		?>
			<script>
				localStorage.setItem('login', "<?php echo $_SESSION['login_id']; ?>");
				localStorage.setItem('dbname', "<?php echo $_SESSION['dbname']; ?>");
			</script>
		<?php
		if(isset($_REQUEST["url"]))
		{
			echo "19 :";
			$redURL = $_REQUEST["url"];
			echo "20 :";
			$DecodeURL = base64_decode($redURL);
			echo "21 :";
			strstr($DecodeURL, "&SID=");
			echo "22 :";
			$pos = strrpos($DecodeURL, "&SID=",-1);
			$URLSocietyID = substr($DecodeURL, $pos+1);
			//echo $URLSocietyID;
			$_REQUEST["url"] = str_replace('_**_', '&', $_REQUEST["url"]);
			$url =  parse_url($_REQUEST["url"]);
			if(isset($url['View']))
			{ echo " 23 :";
				//Has View params 
				?>
						<script>window.location.href = "<?php echo $_REQUEST["url"]?>";</script>
				<?php	
				
			}
			else
			{
				//Has no View params
				if($_SESSION['role'] ==  ROLE_ADMIN_MEMBER ||  $_SESSION['role'] ==  ROLE_ADMIN || $_SESSION['role'] ==  ROLE_SUPER_ADMIN)
				{
					echo "24 :";
					?>
						<script>window.location.href = "<?php echo $_REQUEST["url"].'&View=ADMIN'?>";</script>
				 <?php	
				}
				else
				{?>
						<script>window.location.href = "<?php echo $_REQUEST["url"].'&View=MEMBER'?>";</script>
				 <?php	
				}
			
			}
		}
		if($_SESSION['unit_id'] == 0)
		{
			echo "25 :";
			if($_SESSION['society_id'] ==127)
			{?>
			<script>window.location.href = "home_s1.php?View=ADMIN";</script>	
			<?php }
		?>
            	<script>window.location.href = "home_s.php?View=ADMIN";</script>
         <?php
		}
		else if(!isset($_REQUEST["url"]))
		{
			?>
            <script>window.location.href = "Dashboard.php?View=MEMBER";</script>
            <?php
		}
		else
		{ echo "26 :"
			?>
            <script>window.location.href = "<?php echo $_REQUEST["url"]?>";</script>
            <?php
		}
	}	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>Untitled Document</title>
</head>
<body>
	<center>
    <?php 
	//echo 'View : ' . $_SESSION['View'];
		if($mapCnt > 0)
		{
			?>
			<h2>Select A Society</h2>
                <form name="initialize" method="post" action="">
                    <select id="mapid" name="mapid" style="width:auto; height:auto;">
                        <?php  echo $mapList = $obj_initialize->combobox("Select maptbl.id, concat_ws(' - ', societytbl.society_name, maptbl.desc) from mapping as maptbl JOIN society as societytbl ON maptbl.society_id = societytbl.society_id JOIN dbname as db ON db.society_id = societytbl.society_id WHERE maptbl.login_id = '" . $_SESSION['login_id'] . "' and societytbl.status = 'Y' and maptbl.status = '2' ORDER BY societytbl.society_name ASC ", $_SESSION['current_mapping']);?>			
                    </select>
                    <br /><br />
                    <input type="hidden" name="mode" value="set" />
                    <input type="submit" value="Select" />
                </form>
                <br /><br /><br />
        	<?php
		}
		
		if($_SESSION['authority'] == "self")
		{
			if($mapCnt == 0)
			{
				?>
				<h2>No Society Has Been Added. Kindly Add/Import A New Society</h2>
                <?php
			}
			?>
		    	<div style="border:2px solid #000;width:50%;"></div>
               	<!--<h3><a href='import.php?imp' style="style=color:#0000FF;" class="fa fa-user fa-fw">Import New Society</a></h3>-->
                <h3><a href='import.php?imp' style="style=color:#0000FF;">Import New Society</a></h3>
				<h3><a href='society.php?imp&add'>Add New Society</a></h3>
				
                <div style="border:2px solid #000;width:50%;"></div>
			<?php
		}
	?>
    	<h3><a href='verifycode.php?imp'>Have A New Code To Link Another Society/Flat ?</a></h3>
        <?php if($mapCnt == 0)
		{
			?>
        <h4 style="padding-top: 11px;"> OR </h4>
        <h3><a href='society_details.php' id="society_details">Enter Society Details</a></h3>
       <?php } ?>               
                        
                        
    </center>
  
  <?php if(isset($_REQUEST['tknexsd']))
  {?>
  		<script>document.getElementById("society_details").click();</script>
  <?php }?>
</body>	
	</html>
<?php //include_once "includes/foot.php"; ?>
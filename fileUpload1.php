<?php
//phpinfo();
require_once("functions.php");
//session_start();
//include_once "GDrive.php";
header('Content-Type: text/html; charset=utf-8');
include_once("classes/include/dbop.class.php");
//error_reporting(7);
try
{	
	global $CLIENT_ID, $CLIENT_SECRET, $REDIRECT_URI;
	$client = new Google_Client();
	$client->setClientId($CLIENT_ID);
	$client->setClientSecret($CLIENT_SECRET);
	$client->setRedirectUri($REDIRECT_URI);
	$client->setScopes('email');
	$dbopRoot = new dbop(true);
	$soc_id = $_SESSION["society_id"];
	$sqlSelect1 = "select * from `dbname` where `society_id`='".$soc_id."'";
	$resDBName = $dbopRoot->select($sqlSelect1);
	
	$objdbop = new dbop(false, $resDBName[0]["dbname"]);
	$sqlSelect = "select * from `society`";
	$bTrace = 1;
	//print_r($m_dbConn);
	//echo "</br>";
	if($bTrace)
	{
	echo $sqlSelect;
	}
	//echo "</br>";
	//print_r($objdbop);
	$res = $objdbop->select($sqlSelect);
	//echo "</br>";
	if($bTrace)
	{
	print_r($res);
	}
	$credentials = $res[0]["GDrive_Credentials"];
	//echo "credentials:".$credentials;
	if($credentials == "")
	{	
		$Initialize = 1;  //1 = get credantial url and user information for first time  where as 0 means no need to initialize again
	}
	else
	{
		$Initialize = 0;
	}
	if($bTrace)
	{
	echo "init:".$Initialize;
	}
	//die();
	if($Initialize == 1)
	{
		
		if($bTrace)
		{
			echo $_GET['code'];
		}
		$authUrl = $client->createAuthUrl();	
		getCredentials($_GET['code'], $authUrl);
		//getCredentials($_GET['4/nOnt557-K_YsJUQ0FV-8AT7yUBJtA0ygk5hVNBQnuFM#'], $authUrl);
		$userName = $_SESSION["userInfo"]["name"];
		$userEmail = $_SESSION["userInfo"]["email"];
	}
	$society_code = $res[0]["society_code"];
	$sFolderName = "W2S_".$society_code;
	}
	catch(Exception $ex)
	{
		$GDriveFlag = 0;
		echo "Exception:".$ex->getMessage();
	}
//die();
?>
<!DOCTYPE html>
<html lang="fi">
<head>
	<title>Logged in</title>
	<meta charset="UTF-8">
</head>
<script type="text/javascript" src="js/jquery_min.js"></script>

<script type="text/javascript">
	$(document).ready(function()
		{
			//alert("test");
			//document.getElementById("frmGDrive").submit();
			$( "#submit" ).trigger( "click" );
		});
</script>
<body>

	<!-- Hello --> <? //echo $userName; ?>
	<br>
	<? echo $userEmail; ?>

	<br><br><br>

	<form name="frmGDrive" id="frmGDrive"  action="GDrive.php" method="POST">
		
		<!-- <input type="file" value="c:/color.txt" name="file_array[]"  required   multiple> -->
<input type="text" name="InitializeGDrive" id="InitializeGDrive"  style="visibility: hidden;">
<input type="text" name="ParentFolderID" id="ParentFolderID" value="<?php echo $_REQUEST["code"]?>"  style="visibility: hidden;">
		<br><br>
		<label for="folderName"  style="visibility: hidden;">Enter Folder name, this either uploads file to an existing folder or creates a new one based on the name</label>
		<br>
		<input type="text" name="folderName" placeholder="" value="<?php  echo $sFolderName ?>" style="visibility: hidden;">
		<br><br>
		<label  style="visibility: hidden;" for="folderDesc">Folder description metadata, optional</label>
		<br>
		<input type="text" name="folderDesc" placeholder="" value="<?php  echo $sFolderName ?>"  style="visibility: hidden;">
		<br><br>
        <!-- <br><br>
		<label for="folderDesc">Folder description metadata, optional</label>
		<br>
		<select name="IdType" id="IdType">
        <option value="0">sujit</option>
        <option value="1">Ayush</option>
        </select> 
		<br><br> -->
		<input type="submit" name="submit" id="submit" value="Continue" style="margin-left: 45%;margin-right: 45%;visibility: hidden;"  >
	</form>
	<?php 
	//error_reporting(0);
	//print_r($_REQUEST);
	//echo $URL = "http://localhost/beta_aws_9/GDrive.php?id=".$_REQUEST["code"];?>
    <!-- <a href="<?php //echo $URL ?>">View Click</a>
	
	<a href="GDrive.php?id=1ZxaAoX97Tkcstdef14VpZiIxl7q3mjET">View Click sujit</a> -->
</body>
</html>
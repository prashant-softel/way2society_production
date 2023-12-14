<?php
include_once("../classes/society.class.php");
include_once("../classes/include/dbop.class.php");
	$dbConn = new dbop();
	$obj_society=new society($dbConn, '');
echo $_REQUEST["method"]."@@@";
if($_REQUEST["method"]=="show" || $_REQUEST["method"]=="edit")
	{
		
	$select_type=$obj_society->selecting();
	echo json_encode($select_type);
	}
if($_REQUEST["method"]=="delete")
	{
	$obj_society->deleting();
	return "Data Deleted Successfully";
	}
	
if($_REQUEST["method"]=="uploadsocietylogo")
{
 	if($_FILES["file"]["name"] != '')
	{
 		$test = explode('.', $_FILES["file"]["name"]);
 		$ext = end($test);
 		$name =$_REQUEST['society_code'].'_society_logo.'. $ext;
 		$location = 'SocietyLogo/main/' . $name;  
		move_uploaded_file($_FILES["file"]["tmp_name"], $location);
		
		//$pathToThumbs_index = '../SocietyLogo/thumb/'. $name;
		//move_uploaded_file($_FILES["file"]["tmp_name"], $pathToThumbs_index);
 		$status = $dbConn->update("update society set `society_logo_thumb`='".$location."', `society_logo_main`= '".$location."' where society_code='".$_REQUEST['society_code']."' and society_id ='".$_SESSION['society_id']."'"); 
		
		$res=0;
		if($status >= 0)
		{
			$res=1;
		}
		else
		{
    		$res=0;
		}
		echo $res;	
	  	//$up_query="update society set `society_logo_thumb`='".$location."', `society_logo_main`= '".$location."' where society_code='".$_REQUEST['society_code']."' and society_id ='".$_SESSION['society_id']."'";
	 	//$data=$dbConn->update($up_query);
					
	}
}	
if($_REQUEST["method"]=="uploadQRCode")
{
 	if($_FILES["file"]["name"] != '')
	{
 		$test = explode('.', $_FILES["file"]["name"]);
 		$ext = end($test);
 		$name =$_REQUEST['society_code'].'_society_QR.'. $ext;
 		$location = 'SocietyLogo/QRCode/' . $name;  
		move_uploaded_file($_FILES["file"]["tmp_name"], $location);
		// $up_query="update society set `society_QR_Code`='".$location."' where society_code='".$_REQUEST['society_code']."' and society_id ='".$_SESSION['society_id']."'";
	 //	$data=$dbConn->update($up_query);
		$status = $dbConn->update("update society set `society_QR_Code`='".$location."' where society_code='".$_REQUEST['society_code']."' and society_id ='".$_SESSION['society_id']."'"); 
	
	$res=0;
	if($status >= 0)
	{
		$res=1;
	}
	else
	{
    	$res=0;
	}
	echo $res;			
}
}	
?>

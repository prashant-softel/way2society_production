<?php


$upload_image_errorlog = "upload_image_errorlog".date();
$logfile = fopen($upload_image_errorlog,'a');

$msg = var_dump($_POST);

$msg .= "<br>Uplaod Image get Called!! POST : ".$_POST['feature'];
fwrite($logfile,$msg);




	$error_msg = "ErrorLog Filr Created";
	$error_msg .= var_dump($_POST);
 	$HOST_NAME = "localhost";
	$DB_USER = "root";
	$DB_PASSWORD = "aws123";
	//$DB_PASSWORD = "";
	$DB_SECURITY_ROOT="security_rootdb";
	$DB_SOCIETY_ROOT="hostmjbt_societydb";
	
	if(isset($_POST['feature']) && $_POST['feature'] == 1 || $_POST['feature'] == 4)
	{
		fwrite($logfile,"Feature 1 And 6 Called!!");
		$conn = mysqli_connect($HOST_NAME, $DB_USER, $DB_PASSWORD,$DB_SECURITY_ROOT);
	}
	else if(isset($_POST['feature']) && ($_POST['feature']  == 2 || $_POST['feature'] == 3 ))
	{
		fwrite($logfile,"Feature 2 And 3 Called!!");
		$conn = mysqli_connect($HOST_NAME, $DB_USER, $DB_PASSWORD,$DB_SOCIETY_ROOT);
	}
	else if($_POST['feature'] == 5 || $_POST['feature'] == 6)
	{
		$dbname = "Connection to ".$_POST['dbname'];
		fwrite($logfile,$dbname);
		$conn = mysqli_connect($HOST_NAME, $DB_USER, $DB_PASSWORD,$_POST['dbname']);
	}
	
	
 	if (!$conn) 
		{
			fwrite($logfile,"Connection Failed");
    		die("Connection failed: " . mysqli_connect_error());
		}
	else
		{
		$msg = "<br>Connected successfully";
		$msg .= "<pre>";
		$msg .= print_r(mysqli_connect_error());
		$msg .= "<pre>";
		
		fwrite($logfile,$msg);
		echo "Connected successfully";
	}
 
 
if($_POST['feature'] == 1)
{
	$target_dir = "/SecuirityApp/VisitorImage/";
	$VisitorEntryID = $_POST['VisitorID'];
	$target_file_name =  getcwd() .$target_dir . basename($_FILES["file"]["name"]);
	createThumbImage($_FILES['file']['tmp_name'], $target_file_name);  

	$error_msg .= " <br>File Name ".$_FILES["file"]["name"]." <br>VIsitor ID " .$_POST['VisitorID'];


	$error_msg .= "<br><br>***************************POST DATA*********************************************";
	$error_msg .= var_dump($_POST);
	$response = array();


	fwrite($logfile,$error_msg);

	fclose($logfile);

	// Check if image file is a actual image or fake image
	if (isset($_FILES["file"])) 
	{
		echo $sql = "UPDATE visitors SET img='".$_FILES["file"]["name"]."' WHERE visitor_id='".$VisitorEntryID."'";
   			if (mysqli_query($conn, $sql))
			{
     			$success = true;
 				 $message = "Successfully Uploaded";
 			} 
			else 
			{
      			$success = false;
 			 $message = "Error while uploading";	
  			}
  			mysqli_close($conn);
			
	}
	$response["success"] = $success;
	$response["message"] = $message;
	echo json_encode($response);
}
else if($_POST['feature'] == 2)
{


	$target_main_dir = "/upload/main/";
	$target_Thumb_dir = "/upload/thumb/";
	
	$providerID =$_POST['ProviderId'];
	
	$error_msg .= " <br>File Name ".$_FILES["file"]["name"]." <br>providerID " .$_POST['ProviderId'];


	$error_msg .= "<br><br>***************************POST DATA*********************************************";
	$error_msg .= var_dump($_POST);
	$response = array();


	

	$target_file_name =  getcwd() .$target_Thumb_dir . basename($_FILES["file"]["name"]);
	$photoPath="../upload/thumb/".$_FILES["file"]["name"];
	$photoThumb="../upload/thumb/".$_FILES["file"]["name"];
	
	createThumbImage($_FILES['file']['tmp_name'], $target_file_name);  
	if (isset($_FILES["file"])) 
	{
		 $sql = "UPDATE `service_prd_reg` SET photo='".$photoPath."',`photo_thumb`='".$photoThumb."' WHERE service_prd_reg_id	='".$providerID."'";
		 $error_msg .=$sql ;
   			if (mysqli_query($conn, $sql))
			{
     			$success = true;
 				 $message = "Successfully Uploaded";
 			} 
			else 
			{
      			$success = false;
 			 $message = "Error while uploading";	
  			}
			$error_msg .=$message;
  			mysqli_close($conn);
			
	}
	fwrite($logfile,$error_msg);

	fclose($logfile);
	$response["success"] = $success;
	$response["message"] = $message;
	
	
	echo json_encode($response);
}
else if($_POST['feature'] == 3)
{
	
	$target_doc_dir = "/Service_Provider_Documents/";
	$providerID =$_POST['ProviderId'];
	$target_file_name =  getcwd() .$target_doc_dir . basename($_FILES["file"]["name"]);
	$DocId= $_POST['doc_id'];
	createThumbImage($_FILES['file']['tmp_name'], $target_file_name);  
	
	$msg = "<br>target_dir".$target_dir."<br>providerID".$providerID."<br>target_file_name".$target_file_name;
	
	fwrite($logfile,$msg);
	if (isset($_FILES["file"])) 
	{
			$sql = "insert into `spr_document`(`service_prd_reg_id`,`document_id`,`attached_doc`) value ('".$providerID."','".$DocId."','".$_FILES["file"]["name"]."')";
   			if (mysqli_query($conn, $sql))
			{
     			$success = true;
 				 $message = "Successfully Uploaded";
 			} 
			else 
			{
      		 $success = false;
 			 $message = "Error while uploading";	
  			}
  			mysqli_close($conn);
			
	}
	$response["success"] = $success;
	$response["message"] = $message;
	echo json_encode($response);
	

}
else if($_POST['feature'] == 4)
{
	$target_dir = "/SecuirityApp/VisitorDoc/";
	$VisitorEntryID = $_POST['VisitorID'];
	$target_file_name =  getcwd() .$target_dir . basename($_FILES["file"]["name"]);
	createThumbImage($_FILES['file']['tmp_name'], $target_file_name);  

	$error_msg .= " <br>File Name ".$_FILES["file"]["name"]." <br>VIsitor ID " .$_POST['VisitorID'];


	$error_msg .= "<br><br>***************************POST DATA*********************************************";
	$error_msg .= var_dump($_POST);
	$response = array();


	fwrite($logfile,$error_msg);

	fclose($logfile);

	// Check if image file is a actual image or fake image
	if (isset($_FILES["file"])) 
	{
		echo $sql = "UPDATE visitors SET Doc_img='".$_FILES["file"]["name"]."' WHERE visitor_id='".$VisitorEntryID."'";
   			if (mysqli_query($conn, $sql))
			{
     			$success = true;
 				 $message = "Successfully Uploaded";
 			} 
			else 
			{
      			$success = false;
 			 $message = "Error while uploading";	
  			}
  			mysqli_close($conn);
			
	}
	$response["success"] = $success;
	$response["message"] = $message;
	echo json_encode($response);
}
else if($_POST['feature'] == 5)
{

	$target_main_dir = "/upload/main/";
	$target_Thumb_dir = "/upload/thumb/";
	
	$EntryID =$_POST['entry_id'];
	
	$error_msg .= " <br>File Name ".$_FILES["file"]["name"]." <br>EntryID " .$_POST['entry_id'];


	$error_msg .= "<br><br>***************************POST DATA*********************************************";
	$error_msg .= var_dump($_POST);
	$response = array();


	

	$target_file_name =  getcwd() .$target_Thumb_dir . basename($_FILES["file"]["name"]);
	$photoPath="../upload/thumb/".$_FILES["file"]["name"];
	$photoThumb="../upload/thumb/".$_FILES["file"]["name"];
	
	createThumbImage1($_FILES['file']['tmp_name'], $target_file_name);  
	if (isset($_FILES["file"])) 
	{
		$sql = "UPDATE item_lended SET item_image = '".$photoThumb."' WHERE id='".$EntryID."'";
		 $error_msg .=$sql ;
   			if (mysqli_query($conn, $sql))
			{
     			$success = true;
 				 $message = "Successfully Uploaded";
 			} 
			else 
			{
      			$success = false;
 			 $message = "Error while uploading";	
  			}
			$error_msg .=$message;
  			mysqli_close($conn);
			
	}
	fwrite($logfile,$error_msg);

	fclose($logfile);
	$response["success"] = $success;
	$response["message"] = $message;
	
	
	echo json_encode($response);
}

else if($_POST['feature'] == 6)
{
	$target_dir = "/SecuirityApp/VisitorImage/";
	$VisitorEntryID = $_POST['entry_id'];
	$target_file_name =  getcwd() .$target_dir . basename($_FILES["file"]["name"]);
	createThumbImage1($_FILES['file']['tmp_name'], $target_file_name);  

	$error_msg .= " <br>File Name ".$_FILES["file"]["name"]." <br>Entry ID " .$_POST['entry_id'];


	$error_msg .= "<br><br>***************************POST DATA*********************************************";
	$error_msg .= var_dump($_POST);
	$response = array();


	

	
	$error_msg .="==<br>FileName :".$_FILES["file"]; 
	// Check if image file is a actual image or fake image
	if (isset($_FILES["file"])) 
	{
		echo $sql = "UPDATE item_lended SET item_image = '".$_FILES["file"]["name"]."' WHERE id='".$VisitorEntryID."'";
   			$result = mysqli_query($conn,$sql);
			
			if($result)
			{
				$error_msg .= "<br>Execute";
     				$success = true;
 				 $message = "Successfully Uploaded";
 			} 
			else 
			{
				$error_msg .= "<br>Not Execute";
      			$success = false;
 			 $message = "Error while uploading";	
  			}
  			
	}
	$error_msg .= '<br>'.mysqli_connect_error();
	$error_msg .= '<pre>';
	$$error_msg .= 'print_r($conn)';
	$error_msg .= '</pre>';
	$error_msg .= 'result';
	$error_msg .= '<pre>';
	$$error_msg .= 'print_r($result)';
	$error_msg .= '</pre>';
	
	$error_msg .= print_r(mysqli_query($conn,$sql));
	$error_msg .= $sql;
	$response["success"] = $success;
	$response["message"] = $message;
	echo json_encode($response);
	fwrite($logfile,$error_msg);
	fclose($logfile);
	mysqli_close($conn);
}

function createThumbImage($file, $thumb_url) 
	{
		//echo "FIle Url  :".$thumb_url;
		//echo "FIle  :".$file;
		list($width, $height) = getimagesize($file); 

		if($width > $height)
		{
			$modwidth = 140; 
			$diff = $width / $modwidth;
			$modheight = 120;
		}
		else
		{
			$modwidth = 120; 
			$diff = $width / $modwidth;
			$modheight = 140;
		}

		$tn = imagecreatetruecolor($modwidth, $modheight); 
		$image = imagecreatefromjpeg($file);
		imagecopyresampled($tn, $image, 0, 0, 0, 0, $modwidth, $modheight, $width, $height); 
		imagejpeg($tn, $thumb_url, 100);
	}
	function createThumbImage1($file, $thumb_url) 
	{
		//echo "FIle Url  :".$thumb_url;
		//echo "FIle  :".$file;
		list($width, $height) = getimagesize($file); 

		if($width > $height)
		{
			$modwidth = 340; 
			$diff = $width / $modwidth;
			$modheight = 320;
		}
		else
		{
			$modwidth = 320; 
			$diff = $width / $modwidth;
			$modheight = 340;
		}

		$tn = imagecreatetruecolor($modwidth, $modheight); 
		$image = imagecreatefromjpeg($file);
		imagecopyresampled($tn, $image, 0, 0, 0, 0, $modwidth, $modheight, $width, $height); 
		imagejpeg($tn, $thumb_url, 100);
	}
	
	fclose($logfile);
?>
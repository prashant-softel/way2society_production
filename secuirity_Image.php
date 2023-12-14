<?php 
header('Access-Control-Allow-Origin: *');
	print_r($_POST);
	if(isset($_REQUEST['feature']))
	{ 
	$HOST_NAME = "localhost";
	$DB_USER = "root";
	$DB_PASSWORD = "aws123";
	$DB_SECURITY_ROOT="security_rootdb";
	$DB_SECURITY= "";

		// Create connection
			$conn = mysqli_connect($HOST_NAME, $DB_USER, $DB_PASSWORD,$DB_SECURITY_ROOT);
		
		
		// Check connection
		if (!$conn) 
		{
    		die("Connection failed: " . mysqli_connect_error());
		}
		echo "Connected successfully";
	
		// Upload Image in mobile app feature wise. 
		// upload Visitor Entry Image
		//if($_REQUEST['feature'] == 1) 
		//{
			date_default_timezone_set('Asia/Kolkata');	
			$current_Time = date('H-i-s');
			$current_Date = date('Y-m-d');
			$file= basename($_FILES['file']['name']);
			$VisitorEntryID ='1';  //  id ==1 ;
			//echo "VIsitorID :".$VisitorEntryID;
			list($txt, $ext) = explode(".", $file);
			$randon_name = $file.".".$ext;
			$kaboom = explode(".", $file); // Split file name into an array using the dot
			$fileExt = end($kaboom);
			$random_name="V_".$VisitorEntryID."_".$current_Date."_".$current_Time;
			$url =$random_name.'.'.$fileExt;
			$url =$random_name.'.jpg';
			$target_path = "/SecuirityApp/VisitorImage/";
			$target_path =  getcwd() .$target_path . basename($url);
			
			// Call function in thumb image function
			createThumbImage($_FILES['file']['tmp_name'], $target_path);  
			//$VisitorEntryID = $_REQUEST['VisitorID'];
			echo $sql = "UPDATE visitors SET img='".$url."' WHERE visitor_id='".$VisitorEntryID."'";
   			if (mysqli_query($conn, $sql))
			{
     			echo "Record updated successfully";
 			} 
			else 
			{
      			echo "Error updating record: " . mysqli_error($conn);
  			}
  			mysqli_close($conn);
		//}
		//upload in visitor dcument if( category is delivery boy )
		
		/*else if($_REQUEST['feature'] == 2)
		{
			date_default_timezone_set('Asia/Kolkata');	
			$current_Time = date('H-i-s');
			$current_Date = date('Y-m-d');
			$file= basename($_FILES['file']['name']);
			$VisitorEntryID = $_REQUEST['VisitorID'];
			list($txt, $ext) = explode(".", $file);
			$randon_name = $file.".".$ext;
			$kaboom = explode(".", $file); // Split file name into an array using the dot
			$fileExt = end($kaboom);
			$random_name="VD_".$VisitorEntryID."_".$current_Date."_".$current_Time;
			$url =$random_name.'.'.$fileExt;
			$url =$random_name.'.jpg';
			$target_path = "/SecuirityApp/VisitorDoc/";
			$target_path =  getcwd() .$target_path . basename($url);
			
			// Call function in thumb image function
			createThumbImage($_FILES['file']['tmp_name'], $target_path);
			$VisitorEntryID = $_REQUEST['VisitorID'];
			 $sql = "UPDATE visitors SET Doc_img='".$url."' WHERE visitor_id='".$VisitorEntryID."'";
   			if (mysqli_query($conn, $sql))
			{
     			echo "Record updated successfully";
 			} 
			else 
			{
      			echo "Error updating record: " . mysqli_error($conn);
  			}
  			mysqli_close($conn);
		}
		
		// upload provider attandance images 
		else if($_REQUEST['feature'] == 3)
		{
			date_default_timezone_set('Asia/Kolkata');	
			$current_Time = date('H-i-s');
			$current_Date = date('Y-m-d');
			$file= basename($_FILES['file']['name']);
			$StaffID = $_REQUEST['StaffID'];
			list($txt, $ext) = explode(".", $file);
			$randon_name = $file.".".$ext;
			$kaboom = explode(".", $file); // Split file name into an array using the dot
			$fileExt = end($kaboom);
			//$random_name="S_10_".$current_Date."_".$current_Time;
			$random_name="S_".$StaffID."_".$current_Date."_".$current_Time;
			$url =$random_name.'.'.$fileExt;
		
			$url =$random_name.'.jpg';
	
			$target_path = "https://way2society.com/SecuirityApp/StaffImages";
			
			$socieryCode =$_REQUEST['SocietyCode'];
			
			mkdir($target_path."/".$socieryCode, 777, true);
    		
			$target_path1 = "/SecuirityApp/StaffImages/".$socieryCode."/";
			$target_path1 =  getcwd() .$target_path1 . basename($url);
			createThumbImage($_FILES['file']['tmp_name'], $target_path1);
			$staffEntryID = $_REQUEST['StaffEntryID'];
			$sql = "UPDATE staffattendance SET entry_image='".$socieryCode."/".$url."' WHERE sr_no='".$staffEntryID."'";
			if (mysqli_query($conn, $sql))
			{
     			echo "Record updated successfully";
 			} 
			else 
			{
      			echo "Error updating record: " . mysqli_error($conn);
  			}
  			mysqli_close($conn);
		}*/
		
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
?>
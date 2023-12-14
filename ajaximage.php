<?php
//include('db.php');
//session_start();
//$session_id='1'; //$session id
$path = "Tips/";

function getExtension($str) 
{

         $i = strrpos($str,".");
         if (!$i) { return ""; } 

         $l = strlen($str) - $i;
         $ext = substr($str,$i+1,$l);
         return $ext;
 }

	$valid_formats = array("jpg", "png", "gif", "bmp","jpeg","PNG","JPG","JPEG","GIF","BMP");
	if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST")
		{
			$name = $_FILES['photoimg']['name'];
			$size = $_FILES['photoimg']['size'];
			
			if(strlen($name))
				{
					list($txt, $ext) = explode(".", $name);
					 $ext = getExtension($name);
					if(in_array($ext,$valid_formats))
					{
					if($size<(3072*1024))
						{
							$actual_image_name = time().substr(str_replace(" ", "_", $txt), 5).".".$ext;
							$tmp = $_FILES['photoimg']['tmp_name'];
							if(move_uploaded_file($tmp, $path.$actual_image_name))
								{
								//mysqli_query($db,"UPDATE users SET profile_image='$actual_image_name' WHERE uid='$session_id'");
									
									echo "<img src='Tips/".$actual_image_name."'  class='preview'><br>";
									echo "<div class='link'><span style='color:black;'>URL : </span>Tips/".$actual_image_name."</div>";
								}
							else
								echo "Fail upload folder with read access.";
						}
						else
						echo "Image file size max 1 MB";					
						}
						else
						echo "Invalid file format..";	
				}
				
			else
				echo "Please select image..!";
				
			exit;
		}
?>
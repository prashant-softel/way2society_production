<?php
echo "Call";
	$errorfile_name = 'image_upload_errorlog.txt';
	//$this->errorLog = $this->errorfile_name;
	$errorfile = fopen($errorfile_name, "a");
	$errormsg = "starting image upload";
	$msgFormat=$errormsg."\r\n";
	fwrite($errorfile,$msgFormat);
	
	header('Access-Control-Allow-Origin: *');

	/*
		feature : 1 => classified
		feature : 2 => service request
		feature : 3 => Feature Impose Fine
		feature : 4 =>Service Provider
		feature : 5 =>Service Provider Document
		feature : 6 =>Renovation documents
		feature : 7 =>Task
	*/
	if(isset($_REQUEST['feature']))
	{ 
		if($_REQUEST['feature'] == 1) //Feature Classified
		{
			include_once ("classes/include/dbop.class.php");

			$target_path = "ads/";
		 
			$target_path = $target_path . basename($_FILES['file']['name']);
		 
			if (move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) 
			{
				$classified_id = $_REQUEST['classified_id'];
				$dbConnRoot = new dbop(true);

				$selectClassified = "Select * from `classified` where  id = '" . $classified_id . "'";
				$selectResult = $dbConnRoot->select($selectClassified);

				if($selectResult <> '')
				{
					$img = $selectResult[0]['img'];

					if($img <> '')
					{
						$imgArray = explode(',', $img);

						array_push($imgArray, basename($_FILES['file']['name']));

						$updateClassified = "Update `classified` SET `img` = '" . implode(',', $imgArray) . "' where id = '" . $classified_id . "'";
					}
					else
					{
						$updateClassified = "Update `classified` SET `img` = '" . basename($_FILES['file']['name']) . "' where id = '" . $classified_id . "'";
					}

					$updateResult = $dbConnRoot->update($updateClassified);
				}

		    	echo "Upload and move success";
			} 
			else 
			{
				echo $target_path;
		    	echo "There was an error uploading the file, please try again!";
			}
		}
		else if($_REQUEST['feature'] == 2) //Feature Service Request
		{
			include_once ("classes/include/dbop.class.php");

			$target_path = "upload/main/";
		 
			$target_path = $target_path . basename($_FILES['file']['name']);
		 
			if (move_uploaded_file($_FILES['file']['tmp_name'], $target_path))
			{
				$service_request_id = $_REQUEST['service_request_id'];

				$dbName = getDBName($_REQUEST['token'], $_REQUEST['tkey']);

				$dbConn = new dbop(false, $dbName);

				$selectServiceRequest = "Select * from `service_request` where `request_id` = '" . $service_request_id . "'";
				$selectResult = $dbConn->select($selectServiceRequest);

				if($selectResult <> '')
				{
					$img = $selectResult[0]['img'];

					if($img <> '')
					{
						$imgArray = explode(',', $img);

						array_push($imgArray, basename($_FILES['file']['name']));

						$updateServiceRequest = "Update `service_request` SET `img` = '" . implode(',', $imgArray) . "' where `request_id` = '" . $service_request_id . "'";
					}
					else
					{
						$updateServiceRequest = "Update `service_request` SET `img` = '" . basename($_FILES['file']['name']) . "' where `request_id` = '" . $service_request_id . "'";
					}

					$updateResult = $dbConn->update($updateServiceRequest);
				}

		    	echo "Upload and move success";
			} 
			else 
			{
				echo $target_path;
		    	echo "There was an error uploading the file, please try again!";
			}
		}
		else if($_REQUEST['feature'] == 3) //Feature Impose Fine
		{
			//$errorfile_name = 'image_upload_errorlog_'.date("d.m.Y").'.html';
			//$this->errorLog = $this->errorfile_name;
			//$errorfile = fopen($errorfile_name, "a");
			$errormsg = "starting image upload from impose fine";
			$msgFormat=$errormsg."\r\n";
			fwrite($errorfile,$msgFormat);
			include_once ("classes/include/dbop.class.php");

			$target_path = "impose_Img/";
		 
			$target_path = $target_path . basename($_FILES['file']['name']);
		 
			if (move_uploaded_file($_FILES['file']['tmp_name'], $target_path))
			{
				$fine_id = $_REQUEST['fine_id'];

				$dbName = getDBName($_REQUEST['token'], $_REQUEST['tkey']);

				$dbConn = new dbop(false, $dbName);

				//$selectImposeFine = "Select * from `impose_fine` where `impose_id` = '" . $fine_id . "'";
				$selectImposeFine = "Select * from `reversal_credits` where `ID` = '" . $fine_id . "'";
				$selectResult = $dbConn->select($selectImposeFine);

				if($selectResult <> '')
				{
					$img = $selectResult[0]['img'];

					if($img <> '')
					{
						$imgArray = explode(',', $img);

						array_push($imgArray, basename($_FILES['file']['name']));

						$updateImposeFine = "Update `reversal_credits` SET `img_attachment` = '" . implode(',', $imgArray) . "' where `ID` = '" . $fine_id . "'";
					}
					else
					{
						$updateImposeFine = "Update `reversal_credits` SET `img_attachment` = '" . basename($_FILES['file']['name']) . "' where `ID` = '" . $fine_id . "'";
					}

					$updateResult = $dbConn->update($updateImposeFine);
				}

		    	echo "Upload and move success";
			}
			 
			else 
			{
				echo $target_path;
		    	echo "There was an error uploading the file, please try again!";
			}
		}
		else if($_REQUEST['feature'] == 4) //Service Provider
		{
			include_once ("classes/include/dbop.class.php");

			$main_path = "upload/main/";
			$thumb_path = "upload/thumb/";
		 
			$main_path = $main_path . basename($_FILES['file']['name']);
			$thumb_path = $thumb_path . basename($_FILES['file']['name']);
		 	
			if (move_uploaded_file($_FILES['file']['tmp_name'], $main_path))
			{
				$Service_prd_id = $_REQUEST['serviceProvider_Id'];

				//$dbName = getDBName($_REQUEST['token'], $_REQUEST['tkey']);

				$dbConnRoot = new dbop(true);

				//$selectImposeFine = "Select * from `impose_fine` where `impose_id` = '" . $fine_id . "'";
				$selectImposeFine = "Select * from `service_prd_reg` where `service_prd_reg_id	` = '" . $Service_prd_id . "'";
				$selectResult = $dbConnRoot->select($selectImposeFine);

				if($selectResult <> '')
				{
					$photo = $selectResult[0]['photo'];
					$thumb_photo = $selectResult[0]['photo_thumb'];

					if($photo <> '' || $thumb_photo <> '' )
					{
						//$imgArray = explode(',', $img);

						//array_push($imgArray, basename($_FILES['file']['name']));

						$updateServiceProvider = "Update `service_prd_reg` SET `photo` = '../upload/main/" . basename($_FILES['file']['name']) . "',`photo_thumb`='../upload/thumb/" . basename($_FILES['file']['name']) . "' where `service_prd_reg_id` = '" . $Service_prd_id . "'";
					}
					else
					{
						$updateServiceProvider = "Update `service_prd_reg` SET `photo` = '../upload/main/" . basename($_FILES['file']['name']) . "',`photo_thumb`='../upload/thumb/" . basename($_FILES['file']['name']) . "' where `service_prd_reg_id` = '" . $Service_prd_id . "'";
					}

					$updateResult = $dbConnRoot->update($updateServiceProvider);
				}

		    	echo "Upload and move success";
			}
			 
			else 
			{
				echo $target_path;
		    	echo "There was an error uploading the file, please try again!";
			}
		}
		else if($_REQUEST['feature'] == 5) //Service Provider Document
		{
			include_once ("classes/include/dbop.class.php");

			$target_path = "Uploaded_Documents/";
		 
			$target_path = $target_path . basename($_FILES['file']['name']);
		 
			if (move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) 
			{
				$Service_prd_id = $_REQUEST['serviceProvider_Id'];
				$Document_id = $_REQUEST['documentId'];
				$dbConnRoot = new dbop(true);

				$selectDocuments = "Select * from `spr_document` where `service_prd_reg_id` = '" . $Service_prd_id . "' and `document_id`='" .$Document_id. "'";
				$selectResult = $dbConnRoot->select($selectDocuments);

				if($selectResult <> '')
				{
					$attach_doc = $selectResult[0]['attached_doc'];

					if($img <> '')
					{
						//$imgArray = explode(',', $img);

						//array_push($imgArray, basename($_FILES['file']['name']));

						$updateDocument = "Update `spr_document` SET `attached_doc` = '" . basename($_FILES['file']['name']) . "' where service_prd_reg_id = '" . $Service_prd_id . "' and `document_id`='" .$Document_id. "'";
					}
					else
					{
						$updateDocument = "Update `spr_document` SET `attached_doc` = '" . basename($_FILES['file']['name']) . "' where service_prd_reg_id = '" . $Service_prd_id . "' and `document_id`='" .$Document_id. "'";
					}

					$updateResult = $dbConnRoot->update($updateDocument);
				}

		    	echo "Upload and move success";
			}
			 
			else 
			{
				echo $target_path;
		    	echo "There was an error uploading the file, please try again!";
			}
		}
		else if($_REQUEST['feature'] == 6) //Renovation documents
		{
			try{
				//$errormsg = "starting image upload from Renovation";
				//$msgFormat=$errormsg."\r\n";
			
				include_once ("classes/include/dbop.class.php");
				$renovationId = $_REQUEST['renovationId'];
				//$msgFormat .= "Renovation :" .$renovationId;
				$unitId = $_REQUEST['unitId'];
				//$msgFormat .= "unit :" .$unitId;
				$dbConnRoot = new dbop(true);
				$dbName = getDBName($_REQUEST['token'], $_REQUEST['tkey']);
				$dbConn = new dbop(false, $dbName);
				//$msgFormat .=$_FILES['file']['name'];
				$fileName = "renovationDrawingFile_".$renovationId."_".basename($_FILES['file']['name']);
				//$msgFormat .= "File Name :" .$fileName;
				$uploaddir = "";
				if($_SERVER['HTTP_HOST'] == "localhost" )
				{		
					$uploaddir = $_SERVER['DOCUMENT_ROOT']."/beta_aws_master/Uploaded_Documents";			   
				}
				else
				{
					$uploaddir = $_SERVER['DOCUMENT_ROOT']."/Uploaded_Documents";			   
				}
				//$msgFormat .= "Path :".$uploaddir;
				
				$uploadfile = $uploaddir ."/". $fileName;	
				//$msgFormat .= "file :".$uploadfile;
				$fileResult = move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile);
				//echo "<br>fileResult : ".$fileResult;
				//$msgFormat .= "result :".$fileResult;
				if($fileResult)
				{
					 $insert_query="insert into `documents` (`Name`, `Unit_Id`,`refID`,`Category`, `Note`,`Document`,`source_table`,`doc_type_id`,`doc_version`,`attachment_gdrive_id`) values ('Drawing File', '".$unitId."','".$renovationId."','0','','".$fileName."','2','9','1','')";
					$data = $dbConn->insert($insert_query);
				}
			//$msgFormat .= $insert_query;
			//fwrite($errorfile,$msgFormat);
			}
			catch(exception $ex)
			{
				$ex= getErrorMassage();
				fwrite($errorfile,$ex);
				$errormsg .= $ex;
			}
			fclose($errorfile);
		}
		else if($_REQUEST['feature'] == 7) //Task
		{

		echo "Feature Task";		
			include_once ("classes/include/dbop.class.php");

			//$target_path = "Uploaded_Documents/Tasks";
			echo $society_id = $_REQUEST['society_id'];
			echo $task_id = $_REQUEST['task_id'];
		    $target_path = "../Uploaded_Documents/Tasks/".$society_id."/Task_id_".$task_id."/"; 
			$target_path = $target_path . basename($_FILES['file']['name']);
			$dbName = getDBName($_REQUEST['token'], $_REQUEST['tkey']);
			$dbConn = new dbop(false, $dbName);
			/*if(!file_exists($target_path))
			{
				mkdir($target_path, 0777, true);
			}
			else
			{
				chmod($target_path, 0777,true);
			}*/
			
			
			if (move_uploaded_file($_FILES['file']['tmp_name'], $target_path))
			{
				
				$dbName = getDBName($_REQUEST['token'], $_REQUEST['tkey']);

				$dbConn = new dbop(false, $dbName);

				//$selectImposeFine = "Select * from `impose_fine` where `impose_id` = '" . $fine_id . "'";
				$selectTask = "Select * from `tasklist` where `ID` = '" . $task_id . "'";
				$selectResult = $dbConn->select($selectTask);

				if($selectResult <> '')
				{
					$update_query = "UPDATE tasklist SET Attachment = '".basename($_FILES['file']['name'])."' where id = '".$task_id."'";
					
					$updateResult = $dbConn->update($update_query);
				}

		    	echo "Upload and move success";
			}
			 
			else 
			{
				echo $target_path;
		    	echo "There was an error uploading the file, please try again!";
			}
			
		
	}
	function getDBName($token, $tkey)
	{
		$dbName = "";

		try
		{
			$sURL = "http://way2society.com:8080/Way2Society/Database/get?token=" . $token . "&tkey=" . $tkey;

			$ch = curl_init();

			$headers = array(
			    'Accept: application/json',
			    'Content-Type: application/json',
			);

			curl_setopt($ch, CURLOPT_URL, $sURL);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_getinfo($ch, CURLINFO_HEADER_OUT);
			curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET"); 
		    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($ch);
			curl_close($ch);

			//var_dump($response);

			$aryResponse = json_decode($response, true);

			if($aryResponse['success'] == 1)
			{
				$dbName = $aryResponse['response']['dbname'];
			}
		}
		catch(Exception $exp)
		{
			var_dump($exp);
		}

		return $dbName; 
	}
?>

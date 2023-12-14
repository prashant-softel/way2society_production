<?php

$errorfile_name = 'image_upload_errorlog.txt';
echo file_put_contents($errorfile_name,"Hello World. Testing!");
$errorfile = fopen($errorfile_name, "a");
$errormsg = "inside GDriveTest";
$msgFormat=$errormsg."\r\n";

fwrite($errorfile,$msgFormat);

if(include_once('google-api-php-client/src/Google/Client.php'))
{
$errormsg = "done client loading..";
$msgFormat=$errormsg."\r\n";
fwrite($errorfile,$msgFormat);

}
if(include_once('google-api-php-client/src/Google/Service/Oauth2.php'))
{
$errormsg = "done oath2 loading..";
$msgFormat=$errormsg."\r\n";
fwrite($errorfile,$msgFormat);

}
if(include_once('google-api-php-client/src/Google/Service/Drive.php'))
{
	$errormsg = "done Drive loading..";
	$msgFormat=$errormsg."\r\n";
	fwrite($errorfile,$msgFormat);
}
//error_reporting(7);
//session_start();
$errormsg = "include gdrive complete";
$msgFormat=$errormsg."\r\n";
fwrite($errorfile,$msgFormat);

//include_once("classes/include/dbop.class.php");
//$objConn = new dbop($m_dbConn);
//$res= $objConn->select("select * from society");
//print_r($res);
header('Content-Type: text/html; charset=utf-8');
// Init the variables
//include_once("classes/notice.class.php");
//include_once("classes/utility.class.php");
//include_once("classes/CDocumentsUserView.class.php");
$errormsg = "include all complete";
$msgFormat=$errormsg."\r\n";
fwrite($errorfile,$msgFormat);


//$display_notices=$obj_notice->FetchAllNotices($_REQUEST['in']);
//echo "<pre>";
//print_r($display_notices);
//echo "</pre>";


class GDriveTest
{
	public $m_dbConn;
	public $m_service;
	public $m_UnitNo;
	public $m_sRootFolderID;
	public $m_bShowTrace;
	public $m_notice;
	public $m_objUtility;
	public $m_bSetupInProgress;
	
	function __construct($dbConn, $UnitNo = 0, $rootid = "", $ShowTrace = 0, $bSetupProcess = 0)
	{
		//print_r($dbConn);
		$errorfile_name = 'image_upload_errorlog_'.date("d.m.Y").'.html';
			
		if($ShowTrace)
		{
			//$this->errorLog = $this->errorfile_name;
			//$errorfile = fopen($errorfile_name, "a");
		}
		$this->m_dbConn = $dbConn;
		$this->m_UnitNo = $UnitNo;
		$this->m_sRootFolderID = $rootid;
		//echo "before Initialize".$UnitNo ;
		if($ShowTrace)
		{
			echo "inside Initialize";
			$errormsg = "inside gdrive ctor";
			$msgFormat=$errormsg."\r\n";
			fwrite($errorfile,$msgFormat);
		}
		$this->m_bShowTrace = $ShowTrace;
		//$this->m_notice = new notice($dbConn);
		//$this->m_objUtility = new utility($dbConn);
		if($ShowTrace)
		{
			echo "before Initialize";

				$errormsg = "inside gdrive ctor";
				$msgFormat=$errormsg."\r\n";
				fwrite($errorfile,$msgFormat);
		}
		$this->m_bSetupInProgress = $bSetupProcess;
		$this->Initialize($bSetupProcess); 
	}
	public function Initialize($bSetupProcess)
	{
		$curdir = "c:/wamp/www";
		$curdir .= dirname($_SERVER['REQUEST_URI'])."/";

		$curdir = "https://way2society.com/conf/GoogleClientId.json";
		if($_SERVER['HTTP_HOST']=="localhost")
		{
			$curdir = "c:/wamp/www/w2s_aws_1/conf/GoogleClientId.json";
			$curdir = "https://way2society.com/conf/GoogleClientId.json";
		
		}
		if($this->m_bShowTrace)
		{
			echo "cururl:".$curdir;
			$errorfile_name = 'image_upload_errorlog_'.date("d.m.Y").'.html';
			//$this->errorLog = $this->errorfile_name;
			$errorfile = fopen($errorfile_name, "a");
			$errormsg = "inside Initialize";
			$msgFormat=$errormsg."\r\n";
			fwrite($errorfile,$msgFormat);
		}
		$json = json_decode(file_get_contents($curdir), true);
		if($this->m_bShowTrace)
		{
			print_r($json);
		}
		//echo "Current dir is: ".$curdir;
		$CLIENT_ID = $json['web']['client_id'];
		$CLIENT_SECRET = $json['web']['client_secret'];
		$REDIRECT_URI = $json['web']['redirect_uris'][0];
		if($this->m_bShowTrace)
		{
			echo "clientid:".$CLIENT_ID;
			$errormsg = " Initialize:".$CLIENT_ID;
			$msgFormat=$errormsg."\r\n";
			fwrite($errorfile,$msgFormat);
		
		}
		if($CLIENT_ID == "")
		{
			echo "client id cannot be empty";
			die();
		}
		// Create a new Client
		$client = new Google_Client();
		$client->setClientId($CLIENT_ID);
		$client->setClientSecret($CLIENT_SECRET);
		$client->setRedirectUri($REDIRECT_URI);
		$client->addScope(
			"https://www.googleapis.com/auth/drive", 
			"https://www.googleapis.com/auth/drive.appfolder",
		"https://www.googleapis.com/auth/drive.file",
		"https://www.googleapis.com/auth/drive.readonly",
		"https://www.googleapis.com/auth/drive.metadata.readonly",
		"https://www.googleapis.com/auth/drive.appdata",
		"https://www.googleapis.com/auth/drive.metadata",
		"https://www.googleapis.com/auth/drive.photos.readonly");
		//echo "generating client";
		// Get the file path from the variable
		//$file_tmp_name = $_FILES["file"]["tmp_name"];
		// Get the client Google credentials
		//$credentials = $_COOKIE["credentials"];
		$credentials = $_COOKIE["credentials"];
		//echo "credentials in cookie:".$credentials;
		$res = $this->m_objUtility->GetGDriveDetails();
		if($this->m_bShowTrace)
		{
			print_r($res);
		}
		$sGdriveRootFolderID = $res[0]["GDrive_W2S_ID"];
		if($sGdriveRootFolderID != "" || $bSetupProcess )
		{
			if($bSetupProcess)
			{
				echo "setup in process";
			}
			$credentials = $res[0]["GDrive_Credentials"];
		
			//echo "Credential:".$credentials;
			// Get your app info from JSON downloaded from google dev console
			// Refresh the user token and grand the privilege
			$client->setAccessToken($credentials);
			$client->setAccessType("offline");
			if($this->m_bShowTrace)
			{
				echo "<pre>";
				print_r($client->getAccessToken());
				echo "</pre>";
			}

			
			// else
			// {
			// 	echo "<br>refresh token is not available.</br>";
			// }
			
			$client->setApprovalPrompt("force");
			//$arToken = array('1' =>  1);
			//echo "<br>artoken:".$arToken;
			//echo "refresh token from access:" .$arToken["token_type"]; 
			//echo "</pre>";
			if($client->isAccessTokenExpired())
			{
				if($this->m_bShowTrace)
				{
				echo "<br>access token is expired.</br>";
				}
			}
			else
			{
				if($this->m_bShowTrace)
				{
					echo "<br>access token is active.</br>";
				}
			}
			if($this->m_bShowTrace)
			{
				echo "refresh token:". $client->getRefreshToken();
			}
			if($client->getRefreshToken() != "")
			{
				if($this->m_bShowTrace)
				{
					//echo "new token:".$NewToken;
				 	echo "<br>refresh token found.</br>";
			 	}
				$strRefreshToken  = $client->getRefreshToken();
				$arsplit = split("/", $strRefreshToken);
				//if($client->revokeToken())
				{
					//echo "revoked";	
				}
				//$arToken["refreshToken"]
				//$strRefreshToken = "";
				if($this->m_bShowTrace)
				{
					echo "old token:".$strRefreshToken;
			 	}
			 	$NewToken = str_replace("\\", "", $strRefreshToken);
				if($this->m_bShowTrace)
				{
					echo "new token:".$NewToken;
				 	echo "<br>refresh token found.</br>";
			 	}
			 	//$NewToken = "1/p3P9cnfUmMLEuAKKYZwF4LS1aq7v5Qy4Xu4yUF7i-iYb3j69K0m5l4zMN2Sx4pwm";
			 	
			 		$client->refreshToken($NewToken);
			 		if($this->m_bShowTrace)
					{
						echo "<br>refreshed ";
					 }
			 		$strNewAccessToken = $client->getAccessToken();
			 		$this->m_objUtility->SetGDriveCredentails($strNewAccessToken);
			 		$client->setAccessToken($strNewAccessToken);
			 	//echo $client->getRefreshToken();
			}
			if($this->m_bShowTrace)
			{
				//echo "<pre>";
				//print_r();
				//echo "</pre>";
			}
			if($this->m_bShowTrace)
			{
				echo "done Initialize";
				$errormsg = "done Initialize";
				$msgFormat=$errormsg."\r\n";
				fwrite($errorfile,$msgFormat);
				
			}
			//echo "access token set";
			//print_r($this->m_service);
			$this->m_service = new Google_Service_Drive($client);
			//print_r($this->m_service);
		}
	}
	public function GetGDriveFolderList($RootID)
	{
		$RootFolder= $this->getRootFolder($RootID);//get root folder name
		if($this->m_bShowTrace)
		{
			echo "root name:".$RootFolder;
		}
		if($RootFolder <> '')
		{
			$arFinal = array();
			 $SubFolder= $this->getSubRootFolder($RootID, true, $arFinal);// get sub root folder name
			 //echo "<pre>";
			 $arData = array( "Documents" => $SubFolder);
			 //print_r($arData); //see array of drive documents on ui
			 //echo "</pre>";
			if($SubFolder<>"")
			{
				//echo "<br>final array is:<br>";
				//print_r($SubFolder);
				//GetFoldersandFiles($this->m_service,$SubFolder);
				 
			$List_of_docs =  array(
					'GDrive' => array(
						$FinalFolders,
					));
				return $arData;
				//return $SubFolder;
			}
		}
	}
	public function ShowGDriveTree($RootID, $ViewMode, $TreeModeType, $UnitID)
	{
		if($ViewMode == 1)
		{
			$arData = $this->GetGDriveFolderList($RootID);
		}
		else if($ViewMode == 2)
		{
			//$UnitID = $_SESSION["unit_id"];
			//echo "unit:".$UnitID;
			$objDocumentView = new CDocumentsUserView($this->m_dbConn, $TreeModeType);
			//$arData = new array("" => $objDocumentView->GetNotices($UnitID));
			//$arData = $objDocumentView->GetNotices($UnitID);
			if($UnitID == 0)
			{
				$arData = $objDocumentView->GetUnitDescriptionsFromNotices($UnitID);
			}
			else
			{
				$arData = $objDocumentView->GetUnitDescriptionsFromNotices($UnitID);
			}
		}
		else
		{
			echo "No data found";
			return "No data found";
		}
		//print_r($arData);
		if(sizeof($arData) > 0)
		{
			return $this->Getdir($arData);
		}

	}
	function GetParentFolderID($FolderName, $parentFolderID)
	{
		$files = $this->m_service->files->listFiles();
		//print_r($files);
		$found = false;

		// Go through each one to see if there is already a folder with the specified name
		foreach ($files['items'] as $item)
		 {
		 	if($this->m_bShowTrace)
			{
		 		echo "<br>CID:".$item['id']." Title:".$item['title'] ;
		 		echo "<br>pID:".$parentFolderID;
		 	}
		 	$CanFind = false;
		 	if($item['id'] == $parentFolderID)
		 	{
		 		echo "<br>parent found";
		 		$CanFind = true;
		 	}
		 	if($CanFind)
		 	{
				if ($item['title'] == $folderName) 
				{
					$found = true;
					echo "<br>folder found:".$folderName;
					return $item['id'];
					break;
				}
			}
		}
	}
	function GetFoldersandFiles($SubFolder)
	{
		$SubFolderFileList=$this->getFileListForSubFolder($SubFolder); // get list of files for sub folder
		 //echo "<pre>";
		 //print_r( $SubFolderFileList);
		 //echo "</pre>";
		 if($SubFolderFileList<> "")
		 {
			 //downloadFile($this->m_service,$SubFolderFileList);
			 FileDownload($SubFolderFileList);
		 }
		 return $SubFolderFileList;
	}
	function GetChildFolderID($ParentFolderID, $expFolderName)
	{
		$pageToken = NULL;
   		if($this->m_bShowTrace)
		{
			echo "getting children:";
		}
		  do 
		  {

		    try 
		    {
		    	  $search = "mimeType = 'application/vnd.google-apps.folder' AND trashed != true";
			      $parameters = array("q" => $search);
			      //$search = "mimeType = 'application/vnd.google-apps.folder' AND trashed != true";
		    	  //$parameters = array("q" => $search);
			
			      if ($pageToken) 
			      {
			        $parameters['pageToken'] = $pageToken;
			      }
			      $children = $this->m_service->children->listChildren($ParentFolderID, $parameters);
			      $bFolderIDFound = 0;
			      foreach ($children->getItems() as $child) 
			      {
			      	$FID= $child->getId();
			      	if($this->m_bShowTrace)
					{
						echo "<br>child id".$FID;
			        }
			       // print 'File Id: ' . $child->getId();
			        $file = $this->m_service->files->get($FID);
						//$SubFolder['SubId'][$counter]=$file->getId();
					if($this->m_bShowTrace)
					{
						echo "<br>Folder temp Title:". $file->getTitle();
					}
			        //print_r($child);
			      	
			      	//$this->GetFolderName($child->getId());
					/*$Subchildren = $this->m_service->files->listFiles($parameters,$FID);
					$CurrentID = "";
					$counter=0;
					for($iCont=0;$iCont<sizeof($Subchildren);$iCont++)
					{
					  	$file = $this->m_service->files->get($Subchildren[$iCont]['id']);
						//$SubFolder['SubId'][$counter]=$file->getId();
						if($this->m_bShowTrace)
						{
							echo "<br>Title:". $file->getTitle();
						}*/
					$CurrentID = "";
					
					if(isset($file))
					{	
						if($file->getTitle() == $expFolderName)
						{
							$CurrentID =  $file->getId();
							if($this->m_bShowTrace)
							{
								echo "<br>exp folder found";
							}
							$bFolderIDFound = 1;
							break;
						}
					  	$counter++;
					}
					if($bFolderIDFound == 1)
					{
						if($this->m_bShowTrace)
						{
						echo "<br>exp folder found, returning";
						}	
						break;
					}
				  }
				  	if($this->m_bShowTrace)
					{
				  	echo "<br>folder id returning :".$CurrentID;
			        }
			        return $CurrentID;
			        //echo "desc:".$child->getDescription();
			        //echo "<pre>";
			        //print_r($child);
			        //print_r($child["modelData"]);
			        //echo "</pre>";
			        //die();
			      
			      $pageToken = $children->getNextPageToken();
		    } 
		    catch (Exception $e) 
		    {
		    	if($e->getCode() == "0")
				{
					echo "Error 001 : Login Expired. Please login again to GDrive.";
				}
				else
				{
			      print "An error occurred: " . $e->getMessage();
			     }
		      $pageToken = NULL;
		    }
		  } while ($pageToken);
	}
	/**
	* Get the folder ID if it exists, if it doesnt exist, create it and return the ID*/
	function getFolderIfExistsElse($folderName, $folderDesc, $parentFolderID, $bSubFolder) 
	{
		// List all user files (and folders) at Drive root
		if($this->m_bShowTrace)
		{
			echo "called getFolderIfExistsElse";
		}
		//print_r($this->m_service);
		try
		{

			//$files = $this->m_service->files->listFiles();
		
			//print_r($files);
			$found = false;
			$CanFind = true;
			 
			 if($parentFolderID != "")
			 {
			 	if($this->m_bShowTrace)
				{
			 		echo "<br>parent folder id not empty for Folder:".$folderName;
			 	}
			 	//if($parentFolderID != $this->m_sRootFolderID && $this->m_sRootFolderID != "")
				{
					
					if($this->m_bShowTrace)
					{
						echo "<br>parent folder id not root id";
					 }
					 $FolderID = $this->GetChildFolderID($parentFolderID, $folderName);	
					 if($this->m_bShowTrace)
					 {
					 	echo "<br>parent folder id found";
					 }
					 if($FolderID == "")
					 {
					 	$found = false;
					 }
					 else
					 {
						$found = true;
						return $FolderID;
					 }
				}
				//else
				{
				//	$found = true;
				}
			 }
			 if($this->m_bShowTrace)
			 {
			 	echo "<br>childid:".$childID ." found:".$found;
			 }
			 //die();
			// Go through each one to see if there is already a folder with the specified name
			//foreach ($files['items'] as $item)
			 //{
			 	if(!$bSubFolder)
			 	{

			 	}
			 	//$ParentID = $this->GetParentFolderID();
			 	if($this->m_bShowTrace)
				{
					echo "<br>CID:".$item['id']." Title:".$item['title'] ;
				 	echo "<br>pID:".$parentFolderID;
				 	echo "<pre>";
				 	//print_r($item);
				 	echo "parent id:". $item["modelData"]['parents'][0]["id"];
				 	echo "</pre>";
				}
				 	/*if ($item['title'] == $folderName && !$bSubFolder)
			 	{
			 		echo "<br>parent found";
			 		
						$found = true;
						$CanFind = true;
			 		return $item['id'];
			 		break;
			 	}*/

			 	if($CanFind)
			 	{
					/*if ($item['title'] == $folderName) 
					{
						$found = true;
						echo "<br>folder found:".$folderName;
						return $item['id'];
						break;
					}*/
				}
			//}
	        //die();
			// If not, create one
			if ($found == false) 
			{
				if($this->m_bShowTrace)
				{
					echo "start creating folder:".$folderName;
				}
				$folder = new Google_Service_Drive_DriveFile();

				//Setup the folder to create
				$folder->setTitle($folderName);
				//echo "1";
				
				if(!empty($folderDesc))
					$folder->setDescription($folderDesc);
				//echo "2";
					
				//Set the parent id if you are creating a sub folder
				if(!empty($parentFolderID))
				{
					$parent = new Google_Service_Drive_ParentReference();
					$parent->setId($parentFolderID);
					$folder->setParents(array($parent));
				}
				//echo "set mime";
				$folder->setMimeType('application/vnd.google-apps.folder');

				//Create the Folder
				try
				 {
					$createdFile = $this->m_service->files->insert($folder, array(
						'mimeType' => 'application/vnd.google-apps.folder',
						));
					
					$sEmailID = $createdFile["modelData"]['owners'][0]["emailAddress"];
					if($this->m_bShowTrace)
					{
						echo "<pre>";
						print_r($createdFile);
						echo "</pre>";
						echo "email:".$sEmailID;
					}

					$sql = "update `society` set `GDrive_Email_ID`='".$sEmailID."'";
					if($this->m_bShowTrace)
					{
						echo $sql;
					}
					if($this->m_bSetupInProgress)
					{
						$res= $this->m_dbConn->update($sql);
					}
					// Return the created folder's id
					return $createdFile->id;
				 } 
				catch (Exception $e)
				 {
				 	if($e->getCode() == "0")
					{
						echo "Error 002 : Login Expired. Please login again to GDrive.";
					}
					else
					{
						print "An error occurred: " . $e->getMessage();
				 	}
				 }
			}
		} 
		catch (Exception $e)
		 {
			print "An error occurred new: " . $e->getMessage();
		 }
	}
	function GetFolderName($folderId) {
	  $pageToken = NULL;

	  do {
	    try {
	      $parameters = array();
	      if ($pageToken) {
	        $parameters['pageToken'] = $pageToken;
	      }
		  echo "listing children";
	      $children = $this->m_service->children->listChildren($folderId, $parameters);

	      foreach ($children->getItems() as $child) 
	      {
	        print 'File Id: ' . $child->getId() . " desc: ".$child->getDescription();
	      }
	      $pageToken = $children->getNextPageToken();
	    } catch (Exception $e) {
	      print "An error occurred: " . $e->getMessage();
	      $pageToken = NULL;
	    }
	  } while ($pageToken);
	}

	function printFilesInFolder($folderId) {
	  $pageToken = NULL;

	  do {
	    try {
	      $parameters = array();
	      if ($pageToken) {
	        $parameters['pageToken'] = $pageToken;
	      }
		  echo "listing children";
	      $children = $this->m_service->children->listChildren($folderId, $parameters);

	      foreach ($children->getItems() as $child) {
	        print 'File Id: ' . $child->getId() . " desc: ".$child->getDescription();
	      }
	      $pageToken = $children->getNextPageToken();
	    } catch (Exception $e) {
	      print "An error occurred: " . $e->getMessage();
	      $pageToken = NULL;
	    }
	  } while ($pageToken);
	}
	/**
	 * Insert new file in the Application Data folder. */
	function insertFile($title, $description, $mimeType, $filename, $folderName, $folderDesc, $bUploadFile, &$parentFolderID, $bUpdateSocietyLevel = false)
	 {
		$file = new Google_Service_Drive_DriveFile();

		// Set the metadata
		$file->setTitle($title);
		$file->setDescription($description);
		$file->setMimeType($mimeType);
		
		//$parentFolderID =;

		// Setup the folder you want the file in, if it is wanted in a folder
		if(isset($folderName)) 
		{
			if(!empty($folderName))
			 {
			 	if($this->m_bShowTrace)
				{
			 		echo "folder not empty";
				}
				$parent = new Google_Service_Drive_ParentReference();
				if($this->m_bShowTrace)
				{
					echo "parent is set";
				}
				$parentFolderID = $this->getFolderIfExistsElse($folderName, $folderDesc, $parentFolderID, false); 
				$parent->setId($parentFolderID);
				if($this->m_bShowTrace)
				{
					echo "set parent 1";
				}
				$file->setParents(array($parent));
				if($this->m_bShowTrace)
				{
					echo "<br> parent done:".$parentFolderID;
				}
		
			}
		}
		/*if(!empty($subFolderName)) 
		{
				echo "<br> set subfolder name:".$subFolderName . " its desc :".$subFolderDesc;
			
				$parent = new Google_Service_Drive_ParentReference();
				$parent->setId($this->getFolderIfExistsElse($subFolderName, $subFolderDesc, $parentFolderID , true));
				echo "set parent 2";
				$file->setParents(array($parent));
		}*/
		if($this->m_bShowTrace)
		{
			echo "<br> parent:".$parentFolderID;
		}
		//$objConn = new dbop($m_dbConn);
		echo "bUpdateSocietyLevel:".$bUpdateSocietyLevel;
		if($bUpdateSocietyLevel)
		{
			$sql = "update `society` set `GDrive_W2S_ID`='".$parentFolderID."'";
			//if($this->m_bShowTrace)
			{
				echo $sql;
			}
			$res= $this->m_dbConn->update($sql);
			print_r($res);
		}
		if($this->m_bShowTrace)
		{
			echo "bupload:".$bUploadFile;
		}
		if($bUploadFile == 1 && $bUpdateSocietyLevel == false)
		{
			if($this->m_bShowTrace)
			{
				echo "uploading file:";
			}
			try 
			{
				// Get the contents of the file uploaded
				$data = file_get_contents($filename);

				// Try to upload the file, you can add the parameters e.g. if you want to convert a .doc to editable google format, add 'convert' = 'true'
				$createdFile = $this->m_service->files->insert($file, array(
					'data' => $data,
					'mimeType' => $mimeType,
					'uploadType'=> 'multipart'
					));
//print_r($createdFile);
				// Return a bunch of data including the link to the file we just uploaded
				return $createdFile;
			} 
			catch (Exception $e)
			 {
				print "An error occurred: " . $e->getMessage();
			 }
		}
	}

	//echo "<br>Link to file: " . $driveInfo["alternateLink"];
	function getRootFolder2($rootid) 
	{
		$pageToken = null;
	do {
	    $search = "mimeType = 'application/vnd.google-apps.folder' AND trashed != true";
	    $parameters = array("q" => $search,
	        'fields' => 'nextPageToken, items(id, title)');
		//echo "<pre>";
		//echo "param".print_r($parameters);
		$response = $this->m_service->files->listFiles($parameters);
		$cntr = 1;
		foreach ($response->items as $file) {
			if($this->m_bShowTrace)
			{
				echo "<br>count:".$cntr;
			}
			//print_r($file);
			$cntr++;
			if($this->m_bShowTrace)
			{
				echo "<br>mime:".$file->getMimeType();
		        printf("<br>Found file: %s (%s)\n", $file->name, $file->id);
			}
			$this->printFilesInFolder($file->id);
	    }
		 $pageToken = $repsonse->pageToken;
	} while ($pageToken != null);
	//echo "</pre>";
	    /* $files = $this->m_service->files->listFiles($parameters);
		//print_r($files);
	    echo "</pre>";
		if (!empty($files["items"]))
		 { 
	        $RootFolder['id'] = $files["items"][0]->getId();
			$RootFolder['name'] = $files["items"][0]->getTitle(); // the first element
			//echo $RootFolder['name'];
		 }
		else
		{
	        return false;
		}
		echo "<pre>";
		//print_r($RootFolder);
		echo "</pre>";
		return $RootFolder['id'] ; */
	}

	function getRootFolder($rootid) 
	{
		$sRootID = "";
		try
		{
		    $search = "mimeType = 'application/vnd.google-apps.folder' AND trashed != true";
		    $parameters = array("q" => $search);
			//echo "<pre>";
			//echo "param".print_r($parameters);
			//echo "1";
			//print_r($this->m_service->files);
		    $files = $this->m_service->files->listFiles($parameters);
		    //echo "size:".sizeof($files);
			//print_r($files);
		   // echo "</pre>";
			if (!empty($files["items"]))
			 { 
		        $RootFolder['id'] = $files["items"][0]->getId();
				$RootFolder['name'] = $files["items"][0]->getTitle(); // the first element
				$sRootID = $RootFolder['id'];
				//echo $RootFolder['name'];
			 }
			else
			{
		        return false;
			}
			//echo "2";
			
			if($this->m_bShowTrace)
			{
				echo "<pre>";
				print_r($RootFolder);

			echo "</pre>";
			}
		}
		catch (Exception $e) 
		{
			echo "<pre>";
			//print_r($e);
			//echo "code:".$e->getCode();
			if($e->getCode() == "0")
			{
				echo "Error 003 : Login Expired. Please login again to GDrive.";
			}
			else
			{
	      		print "An error occurred: " . $e->getMessage();
	      	}
	      	$pageToken = NULL;
	      	echo "</pre>";
	    }
		return  $sRootID;
	}

	function getSubRootFolder($RootFolder, $FirstCall, $arFinal, $bExpFolderFound = false)
	{
	   $pageToken = NULL;
	//echo "<pre>";
		  
	  do {
	    try {
	      $parameters = array();
		  if($FirstCall)
		  {
			//$SubFolder =  array();
	      }
		  if ($pageToken) 
		  {
	        $parameters['pageToken'] = $pageToken;
	      }
		  //print_r($RootFolder);
		 // echo "inside SubFolder:".$RootFolder;
	      $children = $this->m_service->children->listChildren($RootFolder, $parameters);
		  if($this->m_bShowTrace)
		  {
		  	echo "<br>children for root folder:".$RootFolder;
		  
			echo "child count:".sizeof($children);
		  }
		  //print_r($children);
		  $counter=0;
		   for($iCont=0;$iCont<sizeof($children);$iCont++)
		   {
			    $file = $this->m_service->files->get($children[$iCont]['id']);
				//echo "<br>MIME Type:".$file["mimeType"];
				
				$MimeType = $file["mimeType"];
				if( $MimeType == "application/vnd.google-apps.folder")
				{
					//print_r($file);
					$FolderID = $file["id"];
					$desc = $file["description"];
					if($this->m_bShowTrace)
					{
						echo "Folder ". $desc . " and current folder:".  $this->m_UnitNo;
					}
					if($this->m_UnitNo == 0 || $this->m_UnitNo == $desc || $bExpFolderFound)
					{
						//echo "<br>Folder found:".$desc;
						if($this->m_bShowTrace)
						{
							echo "<br>matched Folder ". $desc . " with current unit:".  $this->m_UnitNo;
						}
						$sArrayName = $desc;
						$sArraySubName = $FolderID;				
						$sArraySubName = "SubId";
							
						//$SubFolder[$sArraySubName][$file->getId()] = $file->getTitle();//= getSubRootFolder($this->m_service, $FolderID, false);
						//$SubFolder[$sArrayName][$counter]=  getSubRootFolder($this->m_service, $FolderID, false, $arFinal);
						$arFinal[$desc] = $this->getSubRootFolder($FolderID, false, $arFinal[$desc], true);
					}
					else
					{
						$EndOfChildren = sizeof($children) - 1;
						if($iCont == $EndOfChildren)
						{
							if($this->m_bShowTrace)
							{
								echo "<br>Folder ". $desc . " not matched with current unit:".  $this->m_UnitNo;
							}
							$arFinal[$desc] = $this->getSubRootFolder($FolderID, false, $arFinal[$desc], false);
							//continue;
						}
						else
						{
							if($this->m_bShowTrace)
							{
												echo "<br>Folder ". $desc . " not matched with current unit:".  $this->m_UnitNo;
							}				
							continue;
						}
					}
				}
				else
				{
					//echo "<br>Counter:".$iCont."<br>";
				   $sArrayName = "GDrive";
					$sArraySubName = "SubId";
					//echo "<br>Title:".$file->getTitle();;
					$sArrayName = $file->getDescription();
					$sArraySubName = $file->getId();
					$sArraySubName = "SubId";
					$SubFolder[$sArrayName][$file->getId()]= $file->getTitle();
					//$SubFolder[$sArraySubName][$counter]=$file->getId(); //issue with subFolder
					$arFinal[$file->getId()] = $file->getTitle();	
					$counter++;
					$pageToken = $children->getNextPageToken();
					//echo "<pre>";
					//echo "<pre>";
				}
				//$arFinal[$file->getDescription()] = $SubFolder;
		   }
		   //echo "</pre>";
		  
		return $arFinal;
	   }
		catch (Exception $e) 
		{
			print_r($e);
			if($e->getCode() == "0")
			{
				echo "Error 004 : Login Expired. Please login again to GDrive.". $e->getMessage();
			}
			else
			{
	      		print "An error occurred: " . $e->getMessage();
	      	}
	      $pageToken = NULL;
	    }
		
	  } while ($pageToken);
	}

	function getFileListForSubFolder($SubFolder)
	{
	 $pageToken = NULL;
		 do {
		 try {
	      $parameters = array();
	      	if ($pageToken)
			 {
	        	$parameters['pageToken'] = $pageToken;
	     	 }
			//print_r($SubFolder);
		   	for($iCnt=0;$iCnt<sizeof($SubFolder);$iCnt++)
			{
				if($this->m_bShowTrace)
				{
					echo "<br>cnt:".$iCnt."sub folder id:".$SubFolder['SubId'][$iCnt]."<br>";
				}
				$FileList = $this->m_service->children->listChildren($SubFolder['SubId'][$iCnt], $parameters);
				$size = sizeof($FileList);
				if($this->m_bShowTrace)
				{
				echo "Size:".$size;
				}
				if($size > 0)
				{
				//print_r($FileList);
				$k=0;
				 for($i=0;$i<sizeof($FileList);$i++)
				 {
				 	if($this->m_bShowTrace)
					{
						echo "<br>cntr:".$i . "";
					}
					$file = $this->m_service->files->get($FileList[$i]['id']);
					if($this->m_bShowTrace)
					{
						echo "file:".$file;
					}
					$FileName['FileId'][$k]=$file->getId();
					//$FileName['FileName'][$k]= $file->getTitle();
					//$FileName['downloadUrl'][$k]= $file->getDownloadUrl();
				 
			//echo "<pre>";
			//	print_r( $FileName);
			//echo "</pre>";
					$k++;
				 }
				}
			//$pageToken = $children->getNextPageToken();
			}
		
		 }
		catch (Exception $e) 
		{
			if($e->getCode() == "0")
			{
				echo "Error 005 : Login Expired. Please login again to GDrive.";
			}
			else
			{
	      		print "An error occurred: 3 " . $e->getMessage();
	      	}
	      $pageToken = NULL;
	    }
		return  $FileName;
	  } while ($pageToken);
	}

	/*function downloadFile($this->m_service, $FileName) 
	{
		$downloadUrl = $file->getDownloadUrl();	
		if ($downloadUrl) 
		{
			$request = new Google_HttpRequest($downloadUrl, 'GET', null, null);
			$httpRequest = Google_Client::$io->authenticatedRequest($request);
			if ($httpRequest->getResponseHttpCode() == 200)
			{

			return $httpRequest->getResponseBody();
			} 
			else 
			{
				// An error occurred.
				return null;
			}
		}
	 else
	   {
		// The file doesn't have any content stored on Drive.
		return null;
	   }
	}*/
	function printFile($file_id) {
		echo '<pre>';
		print_r($file_id);
		echo '</pre>';
	  //try {
		 // for($i=0;$i<sizeof($file_id);$i++)
		  //{
			// echo "FileID :".$file_id; 	
	    	//$file = $this->m_service->files->get($file_id);
			echo '<pre>';
			print_r($file);
			echo '</pre>';
	    print "Title: " . $file->getTitle();
	    print "Description: " . $file->getDescription();
	    print "MIME type: " . $file->getMimeType();
		 //}
	 // } catch (Exception $e) {
	  //  print "An error occurred: " . $e->getMessage();
	  //}
	  
	}
	function CreateUnitFolder()
	{
		$List_of_documents = array
				(
					'UnitNo' => array(
						'101' => 'event_path_1.pdf',
					),
					'Notices' => array(
						'subject 1' => 'notice_path_1.pdf',
						'subject 2' => 'notice_path_2.pdf'
					),
					/* 'test' => array(
						'one' => 'one-nine-1.doc',
						'two' => 'two-nine-2.docx',          
						'three' => array(   
							'one' => 'one-nine-three-1.ppt',
							'two' => 'two-nine-three-2.pptx'
						), 
					),*/
				);
	}
	function FileDownload($fileList)
	{ 
	try {
	 //echo "test";
	 //echo '<pre>';
	//print_r($fileList);
	 //echo '</pre>';
	//https://drive.google.com/open?id=0B9ez4Vc-n0DbWkV6VmtRZFJIbnhqU3d2QmNHTTZfWWJYZGM0
	//$folder_id='1YdvXnwE2dQTfugwenRzV2RNEmusyO9IY';
	//$file_id='14DeSs_ySFo-UAmc0YRH34U0Ovlw-g9xs';


	//printFile($this->m_service,$fileList);
	//$file = $this->m_service->files->get($file_id);
	//echo "<pre>";
	//print_r($file);
	//echo "</pre>";
	//header('Content-Type: '.$file->getMimeType());
	//print(downloadFile($this->m_service,$file));


	  } catch (Exception $e) {
	  print "An error occurred1: " . $e->getMessage();
	  }
	}
	function GetNotices()
	{
		$display_notices = $this->m_notice->FetchAllNotices("");
		//echo "<pre>";
		//print_r($display_notices);
		$arNotices = array();
		foreach ($display_notices as $key => $value) 
		{
			//echo "key:".$key;
			//if(isset($value["note"]) && $value["note"] != "")
			{
				//echo "link:".$value["note"];
				
				$arNotices[$value["id"]] = $value["subject"];
				//$arNotices[$value["subject"]] = $value["note"];
				//echo "subject:".$value["subject"];
				//echo $value["subject"] ."=>". $value["note"];
			}
		}
		//print_r($arNotices);
		return $arNotices;
		//echo "</pre>";

	}
	function Getdir($GDocuments)
	{
		//notices = $this->GetNotices();
		//echo "<pre>";
		//print_r($GDocuments);
		//echo "</pre>";
		//echo "<br>";
			/*$List_of_documents = array
			(
				'Events' => array(
					'event 1' => 'event_path_1.pdf',
				),					
			);*/
			$List_of_documents = array
			(					
			);
			//echo "<pre>";
			//print_r($List_of_documents);
			//echo "</pre>";
		
			$List_of_documents["Notice"] =$notices;
			/* $List_of_GDrive_documents	= array('Documents' => array(
					'doc 1' => 'doc_path1.gif',
					'doc 2' => 'doc_path2.png',
					'doc 3' => 'doc_path3.jpg'
				),
				); */
				if($this->m_bShowTrace)
				{
					echo "<pre>";
		
					print_r($GDocuments);
					echo "</pre>";
		
				}
				//$List_of_GDrive_documents = array($GDocuments);
			$arMain = array_merge($List_of_documents, $GDocuments);	
			//echo "<pre>";
			//print_r($arMain);
			//echo "</pre>";
			return $this->display_directory_org($arMain, true, false, false);
		
	}
	function display_directory($List_of_documents, $first_call, $bGDriveTree, $bNotices = false, $DisplayType = 1)
	{
		$TreeUL = "<ul  id=\"sub\"";
		if( $first_call ) { $TreeUL .= " class=\"php-file-tree\""; $first_call = false; }
		$TreeUL .= ">";
		$return_link = "http://phpFileTree/?file=[link]/";
		$cntr = 1;
		$Index = 0;
		foreach($List_of_documents as $key => $value)
		{
			$heading = $List_of_documents[0];
			if($Index == 0 && ($heading == "Notice" || $heading == "Misc" || $heading == "Lease"))
			{

			$Index++;
			//	continue;
			}

			$Index++;
			/*if($heading == "Tenants")
			{
			//echo "<br>head".$heading;
			echo "key:".$key;
			echo "<pre>";

			print_r($value);
			echo "</pre>";
			}*/
			if(is_array($value))
			{

				//echo "<br>key:".$key;
				//echo "<br>folder:".$value;
				//$TreeUL .=  "<li class=\"pft-directory\" id=\"$key\" onclick='ExpandTree(".$key.");'><a href=\"#\">" . htmlspecialchars($key) . "</a>";
				$TreeUL .=  "<li class=\"pft-directory\" id=\"$key\" ><a href=\"#\">" . htmlspecialchars($key) . "</a>";
				//$TreeUL .= "<ul>";
				//echo "<br>calling recursive";
				//$bGDriveTree = false;
				if($key == "Documents")
				{
					$bGDriveTree = true;	
					$bNotices = false;
				}
				else if($key == "Notice" || $key == "No Notice")
				{
					$bNotices = true;
					$bGDriveTree = false;
				}
				$TreeUL .= $this->display_directory($value, false, $bGDriveTree, $bNotices);
				//$TreeUL .= "</ul>";
				$TreeUL .=  "</li>";
			}
			else
			{
				//echo "<br>this file val:".$value;
				$pos = strpos($value, "|");
				//echo "pos:".$pos;
				$NewVal = $value;
				if(isset($pos) && $pos != "" && $pos > 0 )
				{
					$NewVal = substr($value, $pos + 1);

					$heading = substr($value, 0, $pos);
					$value = $NewVal;
				}
				else
				{
					continue;
				}
				//echo "<br>new val:".$NewVal;
				//echo "<br>cntr:".$cntr;
				//echo "<br>size:".sizeof($List_of_documents);
				if($cntr == 1)
				{
					//$TreeUL .= "<ul";
					//$TreeUL .= " class=\"pft-file\"";
					//$TreeUL .= ">";
					//echo "inside 1";
				}				
				$ext = "ext-" . substr($value, strrpos($value, ".") + 1); 
				//echo "<br>new ext:".$ext;
				//echo "<br>new key:".$key;
				//echo "<br>new val:".$value;
				//echo "dir:".$directory;
				
				$link = str_replace("[link]", "$directory" . urlencode($value), $return_link);
				if($bGDriveTree)
				{
					$link = "https://drive.google.com/file/d/".$key . "/view";
					$link = "https://docs.google.com/viewer?srcid=".$key ."&pid=explorer&efh=false&a=v&chrome=false&embedded=true";
				}
				else if($bNotices)
				{
					$link = "https://way2society.com/ViewNotice.php?id=".$value;
				}
				$Title = "";

				if($heading == "Notice")
				{
					$link = "https://way2society.com/ViewNotice.php?id=".$value;	
					$sqlQry = "select subject,doc_id,post_date from notices where id='".$value ."'";
					$resultQry = $this->m_dbConn->select($sqlQry);
					$value = $resultQry[0]["subject"];
					
					//$doc_id = $resultQry[0]["doc_id"];
					//if($doc_id > 0)
					{
						//$sqlQry2 = "select * from document_type where ID='".$doc_id ."'";
						//$resultQry2 = $this->m_dbConn->select($sqlQry2);
						//$sDocType= $resultQry2[0]["doc_type"];
						//if($sDocType == "Fine")
						{

							$PostDate = $resultQry[0]["post_date"];
							$value = GetDisplayFormatDate($PostDate) ." | ".$value;
							//echo "title:".$value;
						}
					}
					$Title = $value;
					
				}
				//echo "<br>heading:".$heading . " value:".$value;
				if($heading == "Misc" || $heading == "Lease")
				{
					//$link = "https://way2society.com/ViewNotice.php?id=".$value;	
					$sqlQry = "select name, attachment_gdrive_id,doc_version,Document,TimeStamp from documents where doc_id='".$value ."'";
					//echo "heading:".$heading;
					if($heading == "Tenants" || $heading == "Lease")
					{
						$sqlQry = "select name, attachment_gdrive_id,doc_version,Document,Unit_Id,TimeStamp from documents where refID='".$value ."'";
					}
					//echo "<br>".$sqlQry;
					$resultQry = $this->m_dbConn->select($sqlQry);
					if($heading == "Misc")
					{
						$value = $resultQry[0]["name"];
					}
					if($value == "")
					{
						$value = "[Untitled Document]";
					}
					$sDocDriveID = $resultQry[0]["attachment_gdrive_id"];
					$sDocVersion = $resultQry[0]["doc_version"];
					$sDocName = $resultQry[0]["Document"];
					$sDocUnitID = $resultQry[0]["Unit_Id"];
					$sTimeStamp = $resultQry[0]["TimeStamp"];
					$datetime = explode(" ",$sTimeStamp);
					$PostDate = $datetime[0];

					//echo "<br>timestamp:".$sTimeStamp . " postdate:".$PostDate . "  docname:".$value;
					$sqlMemberQry = "select member_id from member_main where unit='".$sDocUnitID ."'";
					//echo $sqlMemberQry;
					$resultQry = $this->m_dbConn->select($sqlMemberQry);
					$sMemberID = $resultQry[0]["member_id"];
					
						//		echo "id:".$value." doc".$sDocName. "version:".$sDocVersion;
					
					if($heading == "Lease")
					{
						$link = "view_member_profile.php?scm&id=" .$sMemberID ."&tik_id=". time()."&m&view";
					}
					else
					{

						if($sDocVersion == "1")
						{
							$link = "https://way2society.com/Uploaded_Documents/".$sDocName;	
						}
						else if($sDocVersion == "2")
						{


							if($sDocDriveID == "" || $sDocDriveID == "-")
							{
								//if($sDocName == "1494940262_1865946851.")
								{
								//	echo "test".$heading;
								}
								if($sDocName != "")
								{
									$link = "https://way2society.com/Uploaded_Documents/".$sDocName	;
								}
							}
							else
							{
								$link = "https://drive.google.com/file/d/".$sDocDriveID . "/view";
								$link = "https://docs.google.com/viewer?srcid=".$sDocDriveID ."&pid=explorer&efh=false&a=v&chrome=false&embedded=true";
							}
						}
					}
					
					if($heading == "Lease")
					{
						$sqlTenantQry = "select * from tenant_module where tenant_id='".$value ."'";
						//echo "tenant_sql:".$sqlTenantQry;
						$resTenantQry = $this->m_dbConn->select($sqlTenantQry);
						$TenantName = $resTenantQry[0]["tenant_name"];
						$End_Date = $resTenantQry[0]["end_date"];
					
						$Title = GetDisplayFormatDate( $End_Date) ." | ". $TenantName;
						$value = $Title;
						$Title = "Expiring on " . GetDisplayFormatDate( $End_Date)  . " | Tenant : " . $TenantName;
						$value = $Title;
						
					}
					else
					{
						if($PostDate == '0000-00-00')
						{
							$Title =   $value;
						}
						else
						{
							$Title = GetDisplayFormatDate( $PostDate) ." | ". $value;
						}
						$value = $Title;
						
					}
				}
				//echo "<br>new link:".$link;
				//$link = "<li class=\"pft-file " . strtolower($ext) . "\"><a href=\"$link\" title=\"$key\" target=\"_blank\">" . htmlspecialchars($value) . "</a>&nbsp&nbsp<input type='button' value='Download'></li>";
				//$link = "<li class=\"pft-file " . strtolower($ext) . "\"><a href=\"$link\" title=\"$Title\" target=\"_blank\">" . htmlspecialchars($value) . "</a></li>";
				$link = "<li class=\"pft-file " . strtolower($ext) . "\"><a title=\"$Title\" onclick=\"OpenDocument('$link')\" target=\"_blank\" style=\"cursor: pointer;\">" . htmlspecialchars($value) . "</a></li>";
				$TreeUL .= $link;
					//echo $link;
				if($cntr == sizeof($List_of_documents))
				{
					//$TreeUL .= "</ul>";
					//echo "inside end";
				}	
				$cntr++;
			}
			//echo "<br>";
		}
		$TreeUL .= "</ul>";
		return $TreeUL;
	}
	function display_directory_org($List_of_documents, $first_call, $bGDriveTree, $bNotices = false)
	{
		$TreeUL = "<ul  id=\"main\"";
		if( $first_call ) { $TreeUL .= " class=\"php-file-tree\""; $first_call = false; }
		$TreeUL .= ">";
		$return_link = "http://phpFileTree/?file=[link]/";
		$cntr = 1;
		foreach($List_of_documents as $key => $value)
		{
			//echo "Key=" . $key . ", Value=" . $value;
			
			if(is_array($value))
			{
				//echo "<br>key:".$key;
				//echo "<br>folder:".$value;
				$TreeUL .=  "<li class=\"pft-directory\"><a href=\"#\" id=\"root\">" . htmlspecialchars($key) . "</a>";
				//$TreeUL .= "<ul>";
				//echo "<br>calling recursive";
				//$bGDriveTree = false;
				if($key == "Documents")
				{
					$bGDriveTree = true;	
					$bNotices = false;
				}
				else if($key == "Notice" || $key == "No Notice")
				{
					$bNotices = true;
					$bGDriveTree = false;
				}
				$TreeUL .= $this->display_directory($value, false, $bGDriveTree, $bNotices);
				//$TreeUL .= "</ul>";
				$TreeUL .=  "</li>";
			}
			else
			{
				//echo "<br>this file val:".$this_file;
				//echo sizeof($value);
				//echo "<br>cntr:".$cntr;
				//echo "<br>size:".sizeof($List_of_documents);
				if($cntr == 1)
				{
					//$TreeUL .= "<ul";
					//$TreeUL .= " class=\"pft-file\"";
					//$TreeUL .= ">";
					//echo "inside 1";
				}				

				$ext = "ext-" . substr($value, strrpos($value, ".") + 1); 
				//echo "<br>new ext:".$ext;
				//echo "<br>new key:".$key;
				//echo "<br>new val:".$value;
				//echo "dir:".$directory;
				
				$link = str_replace("[link]", "$directory" . urlencode($value), $return_link);
				if($bGDriveTree)
				{
					$link = "https://drive.google.com/file/d/".$key . "/view";
					$link = "https://docs.google.com/viewer?srcid=".$key ."&pid=explorer&efh=false&a=v&chrome=false&embedded=true";
		
				}
				else if($bNotices)
				{
					$link = "https://way2society.com/ViewNotice.php?id=".$value;
				}
				//echo "<br>new link:".$link;
				//$link = "<li class=\"pft-file " . strtolower($ext) . "\"><a href=\"$link\" title=\"$key\" target=\"_blank\">" . htmlspecialchars($value) . "</a>&nbsp&nbsp<input type='button' value='Download'></li>";
				if($value != "")
				{
				$link = "<li class=\"pft-file " . strtolower($ext) . "\"><a href=\"$link\" title=\"$value\" target=\"_blank\">" . htmlspecialchars($value) . "</a></li>";
				$TreeUL .= $link;
					//echo $link;
				}
				if($cntr == sizeof($List_of_documents))
				{
					//$TreeUL .= "</ul>";
					//echo "inside end";
				}	
				$cntr++;
			}
			//echo "<br>";
		}
		$TreeUL .= "</ul>";
		return $TreeUL;
	}


	function UploadFiles($title, $description, $mimeType, $file_tmp_name, $folderName, $folderDesc, $subFolderName, $subFolderDesc, $parentFolderID,$UnitNo, $bUpdateSocietyLevel = false)
	{
		try
		{
			//$name_array=$_FILES["file_array"]["name"];
			//$name_type=$_FILES["file_array"]["type"];
			
			if($this->m_bShowTrace)
			{
				echo "uploading...";
			}
			//$file_temp_array=$_FILES["file_array"]["tmp_name"];	
			//print_r($file_temp_array);
			$driveInfo = "";
			$subFolderName = "";
			$subFolderDesc = "";
			$parts = explode("//", $folderName);
		
			for($i=0; $i<count($parts); $i++)
			{
				$CurFolderName =$parts[$i];
				if($this->m_bShowTrace)
				{
				echo "<br>count:".$i . " interation:".count($parts);
				}
				$size = count($parts) - 1;
				//echo "size:".$size;
				$bUploadFile = false;
				if($i == $size)
				{
					$bUploadFile = true;
				}
				//echo "can upload:".$bUploadFile;
				$title = $title;
				//$description = "";
				if($this->m_bShowTrace)
				{
					echo "<br>title:".$title . "| desc:".$description . "| FolderName:".$CurFolderName."| ParentID:".$parentFolderID."| mimeType:".$mimeType;
				}
				// Call the insert function with parameters listed below
	//die();
				$driveInfo = $this->insertFile($title, $description, $mimeType, $file_tmp_name, $CurFolderName, $CurFolderName, $bUploadFile, $parentFolderID,$bUpdateSocietyLevel);
//print_r($driveInfo);
			}
			$arStatus = array('status' => 1, 'response' => $driveInfo);
			return $arStatus;
			//}
		}
		catch(Exception $ex)
		{
			$GDriveFlag = 0;
			echo "Exception:".$e->getMessage();
			$arStatus = array('status' => 0, 'response' => $e->getMessage());
			return $arStatus;
		}
	}


}


/*if(isset($_REQUEST['id']))
{
	$rootid=$_REQUEST['id'];
}
else
{
	//echo "connecting db";
	$objConn = new dbop($m_dbConn);
	$sql = "select GDrive_W2S_ID from `society`";
	$res= $objConn->select($sql);
	//print_r($res);
	$rootid = $res[0]['GDrive_W2S_ID'];
	//echo "rootid:".$rootid;
	$ObjGDrive = new GDrive($objConn);
	//echo "Initialize done";
	$ObjGDrive->GetGDriveFolderList($rootid);
	//print_r($_SESSION);
}
if($rootid != "")
{
	
}
else
{
	echo "<br>Root ID not found.";
}*/

// Set the file metadata for drive
//$mimeType = $_FILES["file"]["type"];
//$title = $_FILES["file"]["name"];
//$description = "Uploaded from your very first google drive application!";

if(isset($_FILES['file_array']) || isset($_REQUEST["InitializeGDrive"]))
{
	$GDriveFlag = 1;
	try
	{
		$description="File";
		//print_r($file_temp_array);
		$driveInfo = "";
		$folderName = "W2S";
		$folderDesc = "";
		$parentFolderID = "";
		$bInitialiaze = false;
		if(isset($_REQUEST["InitializeGDrive"]))
		{
			$bInitialiaze = true;
			$bAddFoldersOnly = true;
		}

		if (!empty($_POST["folderName"]))
		$folderName = $_POST["folderName"];
		if (!empty($_POST["folderDesc"]))
		$folderDesc = $_POST["folderDesc"];
		if (!empty($_POST["ParentFolderID"]))
		$parentFolderID = $_POST["ParentFolderID"];
		
		$sqlUnit = "select unit_no from `unit` where unit_id='".$UnitID."'";
		$objConn = new dbop($m_dbConn);
		$resUnit = $objConn->select($sqlUnit);
		//print_r($resUnit);
		$UnitNo = $resUnit[0]["unit_no"];

		$subFolderName = "";
		$subFolderDesc = "";
		//$subFolderName = 'Lease';
		//$subFolderDesc = 'Lease files';
		$mimeType="";
		$description="";
		$file_tmp_name="";

		$ObjGDrive = new GDrive($objConn, $UnitNo, "", 0, 1);
		$str = "W2S/";
		//$name_array=$_FILES["file_array"]["name"];
		//$name_type=$_FILES["file_array"]["type"];
		//$file_temp_array=$_FILES["file_array"]["tmp_name"];	
		
		$parts = explode("/", $str);

		//var_dump($parts);
		//die();
		//for($i=0; $i<count($file_temp_array); $i++)
		//{
			//$title =$name_array[$i];
			//$mimeType=$name_type[$i];
			//$description=$description[$i];
			//$file_tmp_name=$file_temp_array[$i];
			// Get the folder metadata
			
			//$ObjGDrive->ShowGDriveTree($rootid);
			$title = "W2S";
			$description = "test";
			//echo "title:".$title . " desc:".$description;
			// Call the insert function with parameters listed below
//die();
			$arSetupResult = $ObjGDrive->UploadFiles("", "", "", "", $folderName, $folderDesc, "", "", "",$UnitNo, $bInitialiaze);
			$arResponse = $arSetupResult["response"];
			//echo "<pre>";
			//print_r($arSetupResult);
			//echo "</pre>";
			//die();
			//$driveInfo = $ObjGDrive->insertFile($title, $description, $mimeType, $file_tmp_name, $folderName, $folderDesc, $subFolderName, $subFolderDesc, $parentFolderID, $bInitialiaze);
			

		//}
	}
	catch(Exception $ex)
	{
		$GDriveFlag = 0;
		echo "Exception:".$ex->getMessage();
	}
	$RedirectURL = "society.php?id=".$_SESSION['society_id']."&show&imp&GDriveFlag=".$GDriveFlag;
	//$RedirectURL = "fileupload1.php?code=".$parentFolderID;
		header("location: ".$RedirectURL);
	
}

?>

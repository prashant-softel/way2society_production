<?php
include_once("dbconst.class.php");
include_once("include/display_table.class.php");
include_once("utility.class.php");
include_once("../GDrive.php");
class addComment extends dbop
{
	public $m_dbConn;
	public $actionPage = "../viewTasks.php";
	
	public $m_dbConnRoot;
	public $m_bShowTrace;
	public $obj_utility;
	function __construct($dbConn,$dbConnRoot)
	{
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = $dbConnRoot;
		$this->m_bShowTrace = 0;
		$this->obj_utility=new utility($this->m_dbConn, $this->m_dbConnRoot);
	}
	//for adding comment to the database
	public function startProcess()
	{
		$comment = $_POST['comment'];
		$userfile = $_POST['userfile'];
		//$file = $_FILES['userfile']['name'];
		$comment = addslashes($comment);
		
	if(!empty($_FILES['userfile']['name'])){				
		

		
		 $target_path = "../Uploaded_Documents/Tasks/".$_SESSION['society_id']."/Task_id_".$_POST['tId']."/"; 
		 $file_path = $target_path.basename($_FILES['userfile']['name']); 
		 //echo $file_path;
		 //die();
		//echo $_FILES['userfile']['tmp_name']; 
		if(!file_exists($target_path))
		{
			mkdir($target_path, 0777, true);
		}
		//else{
			//chmod($target_path, 0777,true);
		//}
		if(move_uploaded_file($_FILES['userfile']['tmp_name'], $file_path)) {  
			echo "File uploaded successfully!";
			} else{  
			echo "Sorry, file not uploaded, please try again!";  
		} 
	}
		$sql="INSERT INTO `comments`(`CType`,`CRefId`,`Comment`,`PostedBy`,`Status`,`Attachment`) VALUES ('".E_TASK."','".$_POST['tId']."','".$comment."','".$_SESSION['login_id']."','Y','".$file_path."')";
		if($this->m_bShowTrace)
		{
			echo $sql."<br>";
		}
		$res = $this->m_dbConn->insert($sql);
		
		if($this->m_bShowTrace)
		{
			echo $res."<br>";
		}
		$this->actionPage = "../viewTasks.php?taskId=".$_POST['tId'];
	}
	public function getAllComments($taskId)
	{
		$sql = "SELECT c.Id, c.Comment, c.PostedBy,c.TimeStamp ,c.Attachment from comments as c where CType = '".E_TASK."' and CRefId = '".$taskId."' and Status = 'Y'";
		$res = $this->m_dbConn->select($sql);
		$count = sizeof($res);
		$finalRes = array();
		for ($i = 0;$i < $count; $i++)
		{
			$sqlName="Select name from login where login_id = ".$res[$i]['PostedBy'];
			$nameRes = $this->m_dbConnRoot->select($sqlName);
			$sqlDiff = "SELECT TIMEDIFF(NOW(),(SELECT `TimeStamp` FROM `comments` WHERE `Id` =  '".$res[$i]['Id']."' AND `status` = 'Y') ) AS timeDiff";
			$diff = $this->m_dbConn->select($sqlDiff);
			$finalRes[$i]['Id'] = $res[$i]['Id'];
			$finalRes[$i]['Comment'] = $res[$i]['Comment'];
			$finalRes[$i]['Attachment'] = $res[$i]['Attachment'];
			$finalRes[$i]['PostedBy'] = $res[$i]['PostedBy'];
			$finalRes[$i]['Name'] = $nameRes[0]['name'];
			$finalRes[$i]['TimeStamp'] = $res[$i]['TimeStamp'];
			$finalRes[$i]['Diff'] = $diff[0]['timeDiff']; 
		}
		return $finalRes;
	}
	public function deleteComment($taskId)
	{
		$timeStamp = date('d/m/Y h:i:s', time());
		$sql = "update comments set Status = 'N', DeletedBy = '".$_SESSION['login_id']."', DeleteTimeStamp = '".$timeStamp."' where Id = '".$taskId."'";
		$res = $this->m_dbConn->update($sql);
		return $res;
	}
}
?>	
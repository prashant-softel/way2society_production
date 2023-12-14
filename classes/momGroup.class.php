<?php
//include_once("include/dbop.class.php");
include_once("dbconst.class.php");
include_once("include/display_table.class.php");
class momGroup extends dbop
{
	public $actionPage = "../momGroup.php";
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg=new display_table($this->m_dbConn);
		//$this->curdate		= $this->display_pg->curdate();
		//$this->curdate_show	= $this->display_pg->curdate_show();
		//$this->curdate_time	= $this->display_pg->curdate_time();
		//$this->ip_location	= $this->display_pg->ip_location($_SERVER['REMOTE_ADDR']);
		//dbop::__construct();
	}
	public function startProcess()
	{
		$errorExists = 0;
		$gId=urlencode($_POST['id']);
		$gName=urlencode($_POST['groupname']);
		$gDes=urlencode($_POST['groupdes']);
			//echo "Id: ".$gId." Name: ".$gName." Des: ".$gDes;
		$res=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/Servlet1?mode=2&id=$gId&name=$gName&des=$gDes&dbName=".$_SESSION['dbname']);
			///echo "start process: ";
			//print_r($res);
			//$up_query="update `group` set `srno`='".$_POST['srno']."',`groupname`='".$_POST['groupname']."' where id='".$_POST['id']."'";
			//$data = $this->m_dbConn->update($up_query);
		return $res;	
		
	}
	public function combobox($query)
	{
	}
	public function display1($rsas)
	{
		$thheader = array('Name', 'Description');
		$this->display_pg->edit		= "getgroup";
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "momGroup.php";
		if($_SESSION['role']=="Super Admin")
		{
			$res = $this->display_pg->display_datatable($rsas, true, true);
			return $res;
		}
		else
		{
			$res = $this->display_pg->display_datatable($rsas, false, false);
			return $res;
		}
	}
	public function pgnation()
	{
		$res= file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/Servlet1?mode=4&dbName=".$_SESSION['dbname']);
		//print_r ($res);
		
		//$grpName=$_POST['grpName'];
		//$res= file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/Servlet1?mode=5&name=$grpName");
		//echo "<br>data".$res;
		$cRes=array();
		$jRes = json_decode($res,true);
		/*for($i=0;$i<sizeof($jRes);$i++)
		{
			//foreach($value as $k)
			//{
				array_push($cRes[$i],"<a href='createGrp.php'><img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;'/></a>");
				array_push($cRes[$i],$jRes['Name']);
				array_push($cRes[$i],$jRes['Description']);
			//}
		}*/
		//echo "val:<pre>";
		//print_r($cRes);
		//echo "</pre>";
		//echo "Id:".$jRes['id'];
		//echo "Name:".$jRes['name'];
		$data=$this->display1($jRes);
		return $data;
	}
	public function selecting()
	{
		//echo "In selecting:";
		$gId=urlencode($_REQUEST['groupId']);
		//echo "In selecting:".$gId;
		$res=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/Servlet1?mode=6&id=$gId&dbName=".$_SESSION['dbname']);
		$jRes = json_decode($res,true);
		//echo $jRes;
		//print_r($res);
		//$sql = "select id,`srno`,`groupname` from `group` where id='".$_REQUEST['groupId']."'";
		//$res = $this->m_dbConn->select($sql);
		//echo "Result:".$res;
		//$res1=substr($res, 1);
		//echo strlen($res1);
		//echo (strlen($res1) - 1);
		//$res2=substr($res1, 0,(strlen($res1) - 3) );
		//foreach ($res2 as $k => $v) 
		//{
			//$arr[$k]=$v;
		//}
		//echo $jRes;
		//echo $res;
		return $jRes;
	}
	public function deleting($gId)
	{
		$ret="gId:".$gId;
		$strURL = HOST_NAME."8080/MinutesOfMeetingS/servletGrpMember?mode=3&gId=$gId&dbName=".$_SESSION['dbname'];
		echo $strURL;
		$memRes=file_get_contents($strURL);
		$ret.="MemRes: ".$memRes;
		$res=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/Servlet1?mode=3&id=$gId&dbName=".$_SESSION['dbname']);	
		$ret.="GrpRes:".$res;
		return($ret);
	}
}
?>
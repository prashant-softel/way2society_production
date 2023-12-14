
<?php
include_once("include/dbop.class.php");
include_once ("dbconst.class.php"); 
//include_once("include/display_table.class.php");
//include_once "dbconst.class.php";
class selfsignup // extends dbop
{
	public $actionPage = "../selfsignup.php";
	public $m_dbConnRoot;	
	function __construct($dbConnRoot)
	{
		$this->m_dbConnRoot = $dbConnRoot;
		//$this->display_pg=new display_table();

		// $this->curdate		= $this->display_pg->curdate();
		// $this->curdate_show	= $this->display_pg->curdate_show();
		// $this->curdate_time	= $this->display_pg->curdate_time();
		// $this->ip_location	= $this->display_pg->ip_location($_SERVER['REMOTE_ADDR']);

		//dbop::__construct();
	}

	public function startProcess()
	{
		$errorExists = 0;
 			//print_r($_REQUEST);
		// $curdate 		=  $this->curdate;
		// $curdate_show	=  $this->curdate_show;
		// $curdate_time	=  $this->curdate_time;
		// $ip_location	=  $this->ip_location;

		if($_REQUEST['insert']=='Submit' && $errorExists==0)
		{
			$insert_query="insert into `self_sign` (`name`,`contect_no`,`email`,`society_name`,`no_of_units`,`society_add`,`designation`) values ('".$_POST['name']."','".$_POST['number']."','".$_POST['email']."','".$_POST['soc_name']."','".$_POST['no_of_unit']."','".$_POST['soc_add']."','".$_POST['desg']."')";
			
			///die();
			$res = $this->m_dbConnRoot->insert($insert_query);
		//$data = $this->insert($insert_query);
			return "Insert";
		}
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			$up_query="update  set `name`='".$_POST['name']."',`number`='".$_POST['number']."',`email`='".$_POST['email']."',`soc_name`='".$_POST['soc_name']."',`no_of_unit`='".$_POST['no_of_unit']."' where id='".$_POST['id']."'";
			$data = $this->update($up_query);
			return "Update";
		}
		else
		{
			return $errString;
		}
	}
	public function combobox($query)
	{
	}
	public function display1($rsas)
	{
		$thheader = array('name','number','email','soc_name','no_of_unit');
		$this->display_pg->edit		= "getself_sign";
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "self_sign.php";

		$res = $this->display_pg->display_new($rsas);
		return $res;
	}
	public function pgnation()
	{
		$sql1 = "select id,`name`,`number`,`email`,`soc_name`,`no_of_unit` from  where status='Y'";
		$cntr = "select count(status) as cnt from  where status='Y'";

		$this->display_pg->sql1		= $sql1;
		$this->display_pg->cntr1	= $cntr;
		$this->display_pg->mainpg	= "self_sign.php";

		$limit	= "50";
		$page	= $_REQUEST['page'];
		$extra	= "";

		$res	= $this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
		return $res;
	}
	public function selecting()
	{
		$sql = "select id,`name`,`number`,`email`,`soc_name`,`no_of_unit` from  where id='".$_REQUEST['self_signId']."'";
		$res = $this->select($sql);
		return $res;
	}
	public function deleting()
	{
		$sql = "update  set status='N' where id='".$_REQUEST['self_signId']."'";
		$res = $this->update($sql);
	}
}
?>
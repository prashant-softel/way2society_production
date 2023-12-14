<?php if(!isset($_SESSION)){ session_start(); }
include_once("include/display_table.class.php");
include_once ("dbconst.class.php");
//include_once('../swift/swift_required.php');
//include_once( "include/fetch_data.php");
//include_once("utility.class.php");
//include_once("android.class.php");

class soc_note1 extends dbop
{
	public $actionPage = "../society_notes.php";
	public $m_dbConn;
	/*public $m_dbConnRoot;
	public $objFetchData;
	public $obj_Utility;
	public $obj_android;*/
		function __construct($dbConn)
		{
		$this->m_dbConn = $dbConn;
		$this->display_pg=new display_table($this->m_dbConn);
	//	$this->m_dbConnRoot = $dbConnRoot;
		//dbop::__construct();
		
		/*$this->objFetchData = new FetchData($dbConn);
		if(isset($SocietyID) && $SocietyID <> "")
		{
			$this->objFetchData->GetSocietyDetails($SocietyID);
		}
		else
		{
			$this->objFetchData->GetSocietyDetails($_SESSION['society_id']);
		}
		
		$this->obj_Utility = new utility($dbConn, $dbConnRoot);*/
	
	}
	public function addNotes()
	{
		$errorExists=0;
		if($_REQUEST['insert']=='Save' && $errorExists==0)
		{
			$update_query="update society set Account_notes='".$_POST['note_desc']."' where society_id='".$_SESSION['society_id']."'";
			$data = $this->m_dbConn->update($update_query);
			return $data;
			echo "Updated";
		}
		else{
			echo "";
			
			}
		
		
	}
	
	public function AccountsNotes(){
		 $sql1="select Account_notes from society where society_id=".$_SESSION['society_id'];
		 $res=$this->m_dbConn->select($sql1);
		 $accounts_note=$res[0]['Account_notes'];
		// var_dump($res);
		  //foreach($res as $value){
			  return $accounts_note;
			//  }
		 
		 
		
		
		}
	
		 
	
}
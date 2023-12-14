<?php
include_once("dbconst.class.php");
include_once("include/display_table.class.php");
include_once("utility.class.php");
include_once("dbconst.class.php");
include_once("../GDrive.php");
class addLien extends dbop
{
	public $m_dbConn;
	public $actionPage = "../lien.php";
	
	public $m_dbConnRoot;
	public $m_bShowTrace;
	public $obj_utility;
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = $dbConnRoot;
		$this->m_bShowTrace = 0;
		$this->obj_utility=new utility($this->m_dbConn, $this->m_dbConnRoot);
		//$this->display_pg=new display_table($this->m_dbConn);

		//$this->curdate		= $this->display_pg->curdate();
		//$this->curdate_show	= $this->display_pg->curdate_show();
		//$this->curdate_time	= $this->display_pg->curdate_time();
		//$this->ip_location	= $this->display_pg->ip_location($_SERVER['REMOTE_ADDR']);

		//dbop::__construct();
	}
	//used for displaying combobox for unit details 
	public function comboboxForUnitDetails($query,$id)
	{
		//$str.="<option value=''>All</option>";
		$str.="<option value=''>Please Select</option>";
		$data = $this->m_dbConn->select($query);
		if(!is_null($data))
		{
			foreach($data as $key => $value)
			{
				$i=0;
				foreach($value as $k => $v)
				{
					if($i==0)
					{
						if($id==$v)
						{
							$sel = 'selected';	
						}
						else
						{
							$sel = '';
						}
				
						$str.="<OPTION VALUE=".$v.' '.$sel.">";
					}
					else
					{
						$str.=$v."</OPTION>";
					}
					$i++;
				}
			}	
		}
		return $str;
	}
	//used to get member id by unit Id
	public function getMemberId($unitId)
	{
		$sql="SELECT `member_id` FROM `member_main` where `unit` = '".$unitId."' AND `ownership_status` = '1'";
		$memRes=$this->m_dbConn->select($sql);
		$id=$memRes[0]['member_id'];
		return $id;
	}
	//Used to delete document while updating lien
	public function deleteDocument($docId)
	{
		$sql="UPDATE documents set status='N' where doc_id = ".$docId;
		$res=$this->m_dbConn->update($sql);
		return $res;
	}
	//Used for adding and updating lien
	public function startProcess()
	{
		$doc_type = DOC_TYPE_LIEN_ID;		
		if($_REQUEST['btnSubmit'] == "Submit")
		{
			//insert
			/*echo "<pre>";
			print_r($_POST);
			echo "</pre>";*/
			$unitId=$_POST['unitId'];
			//echo "unit:".$unitId;
			$memId=$this->getMemberId($unitId);
			//echo "mem:".$memId;
			//$memAppNo=$_POST['memberApplicationNo'];
			$bankName=$_POST['bankName'];
			$amt = 0;
			$amt=$_POST['loanAmount'];
			$nocDate=getDBFormatDate($_POST['societyNocDate']);
			$loanStatus=$_POST['loanStatus'];
			
			if($loanStatus == LIEN_ISSUED)
			{
				$this->actionPage .= "?type=NOC&unit_id=" . $unitId;
			}
			if(strtolower($loanStatus) == LIEN_OPEN)
			{
				$this->actionPage .= "?type=open&unit_id=" . $unitId;
				$OpeningDate = getDBFormatDate($_POST['loanOpeningDate']);
			}
			else
			{
				$OpeningDate="0000-00-00";
			}
			
			if(strtolower($loanStatus) == LIEN_CLOSED)
			{
				$this->actionPage .= "?type=closed&unit_id=" . $unitId;
				$closingDate=getDBFormatDate($_POST['loanClosingDate']);
			}
			else
			{
				$closingDate="0000-00-00";
			}
			$note=$_POST['note'];
			$timeStamp=date('d/m/Y h:i:s', time());
			$status="Y";
			$sql="INSERT INTO `mortgage_details`(`member_id`,`UnitId`,`BankName`,`Amount`,`SocietyNOCDate`,`OpeningDate`,`CloseDate`,`LienStatus`,`Note`,`Status`,`TimeStamp`) VALUES (".$memId.",'".$unitId."','".$bankName."','".$amt."','".$nocDate."','".$OpeningDate."','".$closingDate."','".$loanStatus."','".$note."','".$status."','".$timeStamp."')";
			if($this->m_bShowTrace == 1)
			{
				echo $sql."<br>";
			}
			/*$sql="'insert into `mortgage_details` (`member_id`,`UnitNo`,`BankName`,`Amount`,`SocietyNOCFile`,`SocietyNOCDate`,`MemberApplication`,`CloseDate`,`LienStatus`,`Note`,`Status`,`TimeStamp`) values ('".$memId."','".$unitId."','".$bankName."','".$amt."','".$noc."','".$nocDate."','".$memAppNo."','".$closingDate."','".$loanStatus."','".$note."','".$status."','".$timeStamp."')';";*/
			$res = $this->m_dbConn->insert($sql);
			//echo $res;
			//uploading files to g drive
			
			$doc=$_POST['doc_count'];
			
		
			if(!empty($_FILES['userfile1']['tmp_name']))
			{
				$subFolderName = "";
				$subFolderDesc = "";
				$mimeType="";
				$description="";
				$file_tmp_name="";
				$today = date("Y-m-d");    // 2018-01-20
	
				$str = $unitId."//Lien//". $nocDate . $bankName." ".$amt;
				if($this->m_bShowTrace==1)
				{
					echo "path:".$str;
				}
				$parts = explode("//", $str);
				$fileName = "";
				
				$RefId=$res;
				for($i=1; $i<=$doc; $i++)
				{
					$today = date("Y-m-d");    // 2018-01-20        		                    
						//$Note = $_POST["note"];
					$docGDriveID = "";
					$random_name = "";
					$doc_name = "";
					
					$doc_name = $_POST["docName".$i];
					//echo $doc_name."<br>";
						//die();
					$resResponse = $this->obj_utility->UploadAttachment($_FILES,  $doc_type, $PostDate, "Uploaded_Documents", $i, true, $UnitNo);
					//echo "Res Responce: <br>";
					//echo "<pre>";
					//print_r($resResponse);
					//echo "</pre>";
					$sStatus = $resResponse["status"];
					$sMode = $resResponse["mode"];
					$sFileName = $resResponse["response"];
					$sUploadFileName = $resResponse["FileName"];
	
					//$doc_name = $resResponse["doc_name"];
					//$random_name = $resResponse["file_name"];
					if($sMode == "1")
					{
						$random_name = $sFileName;
						//$_POST['note'] = $resResponse["note"];
					} 
					else if($sMode == "2")
					{
						$docGDriveID = $sFileName;
					}
					else
					{
						//failure or no file uploaded
					}
					
					$sDocVersion = '2';
					if($GdriveDocID != "")
					{
						$sDocVersion = '1';
					}
					//for lien table source_table=2
					$insert_query="insert into `documents` (`Name`, `Unit_Id`,`refID`,`Category`, `Note`,`Document`,`status`,`source_table`,`doc_type_id`,`doc_version`,`attachment_gdrive_id`) values ('".$doc_name."','".$unitId."','".$RefId."','0', '','".$sUploadFileName."','Y','0','".$doc_type."','".$sDocVersion."','".$docGDriveID."')";
					//echo "ins:".$insert_query;
					$data=$this->m_dbConn->insert($insert_query);
					//echo "<pre>";
					//print_r($data);
					//echo "</pre>";
			}
			
				return "Insert";
			}		
		}
		if($_REQUEST['btnSubmit']=="Update")
		{
			//"update";
			//echo "<pre>";
			//print_r($_POST);
			//echo "</pre>";
			$unitId=$_POST['unitId'];
			//echo "uId:".$unitId;
			$resUnit = $this->obj_utility->GetUnitDesc($unitId);
			$UnitNo = $resUnit[0]["unit_no"];
			$loanStatus=$_POST['loanStatus'];
			$loanAmount = $_POST['loanAmount'];
			
			
			if($loanStatus == LIEN_ISSUED)
			{
				$this->actionPage .= "?type=NOC&unit_id=" . $unitId;
			}
			if(strtolower($loanStatus) == LIEN_OPEN)
			{
				$this->actionPage .= "?type=open&unit_id=" . $unitId;
				$OpeningDate = getDBFormatDate($_POST['loanOpeningDate']);
			}
			else
			{
				$OpeningDate="0000-00-00";
			}
			
			if(strtolower($loanStatus) == LIEN_CLOSED)
			{
				$this->actionPage .= "?type=closed&unit_id=" . $unitId;
				$closingDate=getDBFormatDate($_POST['loanClosingDate']);
			}
			else
			{
				$closingDate="0000-00-00";
			}
			$lienId=$_POST['lienId'];
			$note=$_POST['note'];
			$timeStamp=date('d/m/Y h:i:s', time());
			$status="Y";
			$sql="Update `mortgage_details` set `LienStatus`='".$loanStatus."',`CloseDate`='".$closingDate."', `OpeningDate`= '".$OpeningDate."', `Amount` = '".$loanAmount."', `Note`='".$note."',`TimeStamp`='".$timeStamp."' where Id=".$lienId;
			//echo "Update Query:".$sql;
			$res=$this->m_dbConn->update($sql);
			//echo $res;
			//uploading files to g drive
			//echo "<pre>";
			//print_r($_FILES);
			//echo "</pre>";
			if($_FILES!="")
			{
				$subFolderName = "";
				$subFolderDesc = "";
				$mimeType="";
				$description="";
				$file_tmp_name="";
				$today = date("Y-m-d");    // 2018-01-20
				$str = $UnitNo ."//Lien//".$today;
				//echo $str."<br>";
				if($this->m_bShowTrace==1)
				{
					echo "path:".$str;
				}
				$parts = explode("//", $str);
				$fileName = "";
				$doc=$_POST['doc_count'];
				$RefId=$res;
				$doc_name = $_POST["docName1"];
				if($doc_name != "")
				{
					for($i=1; $i<=$doc; $i++)
					{
						$today = date("Y-m-d");    // 2018-01-20        		                    
					//$Note = $_POST["note"];
						$docGDriveID = "";
						$random_name = "";
						$doc_name = "";
						$doc_name = $_POST["docName".$i];
				//echo $doc_name."<br>";
						//die();
						$resResponse = $this->obj_utility->UploadAttachment($_FILES,  $doc_type, $PostDate, "Uploaded_Documents", $i, $UnitNo);
				//echo "Res Responce: <br>";
				//echo "<pre>";
				//print_r($resResponse);
				//echo "</pre>";
						$sStatus = $resResponse["status"];
						$sMode = $resResponse["mode"];
						$sFileName = $resResponse["response"];
						$sUploadFileName = $resResponse["FileName"];

				//$doc_name = $resResponse["doc_name"];
				//$random_name = $resResponse["file_name"];
						if($sMode == "1")
						{
							$random_name = $sFileName;
						//$_POST['note'] = $resResponse["note"];
						} 
						else if($sMode == "2")
						{
							$docGDriveID = $sFileName;
						}
						else
						{
						//failure or no file uploaded
						}
						$sDocVersion = '2';
						if($GdriveDocID != "")
						{	
							$sDocVersion = '1';
						}
						//for lien table source_table=0
						$insert_query="insert into `documents` (`Name`, `Unit_Id`,`refID`,`Category`, `Note`,`Document`,`status`,`source_table`,`doc_type_id`,`doc_version`,`attachment_gdrive_id`) values ('".$doc_name."','".$unitId."','".$RefId."','0', '','".$sUploadFileName."','Y','0','".$doc_type."','".$sDocVersion."','".$docGDriveID."')";
						//echo "ins:".$insert_query;
						$data=$this->m_dbConn->insert($insert_query);
					//echo "<pre>";
					//print_r($data);
					//echo "</pre>";
						return "Update";
					}
				}		
			}
		}
	}
}
?>	
<?php
include_once("include/dbop.class.php");
include_once("utility.class.php");
include_once("dbconst.class.php");
include_once("include/fetch_data.php");
include_once("changelog.class.php");//Pending - Verify
// include_once("genbill.class.php");
class share_certificate_import 
{
	public $m_dbConn;
	public $obj_utility;
	public $errorfile_name;
	public $errorLog;
	public $actionPage = '../import_share_certificate.php';
	public $changeLog;
	public $obj_genbill;
	private $InvoiceNumberArray = array();
	public $obj_fetch;

	function __construct($dbConnRoot, $dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->obj_utility = new utility($this->m_dbConn);
		$this->m_objLog = new changeLog($this->m_dbConn);
		// $this->obj_genbill = new genbill($this->m_dbConn);
		$this->obj_fetch = new FetchData($this->m_dbConn);

		$a = $this->obj_fetch->GetSocietyDetails($_SESSION['society_id']);
	}
	

	public function ImportData($fileName,$fileData)
	{
		$Foldername = $this->obj_fetch->objSocietyDetails->sSocietyCode;

		if (!file_exists('../logs/import_log/'.$Foldername)) 
		{
			mkdir('../logs/import_log/'.$Foldername, 0777, true);
		}

		$this->errorfile_name = '../logs/import_log/'.$Foldername.'/import_share_certificate_errorlog_'.date("d.m.Y").'_'.rand().'.html';
		$this->errorLog = $this->errorfile_name;
		$errorfile = fopen($this->errorfile_name, "a");
		$errormsg="[Importing Share Certificate Data]";
		$isImportSuccess = true;
		$this->obj_utility->logGenerator($errorfile,'start',$errormsg);

		$array = array();
		$Success = 0;
		//$rowCount = 0;
		foreach($fileData as $row)
			{
				// $size = sizeof($row);
				// print_r($fileData);
				// die();

				if($row[0] || $row[1] <> '')
				{
					$rowCount++;
					if($rowCount == 1)//Header
					{
						$Nameofthemember = array_search(Name_of_the_member, $row, true);
						$Flatno = array_search(Flat_No,$row,true);
						$Sharecertificateno = array_search(Share_Certificate_No, $row, true);
						$Sharecertificatefrom=array_search(Share_certificate_from,$row,true);
						$Sharecertificateto=array_search(Share_certificate_to,$row,true);
						// $Distinctiveno = array_search(Distinctive_No, $row, true);
						$Memberregisterno = array_search(Member_Register_no, $row, true);
						
						// print_r($UnitNo);
						// die();

						if(!isset($Nameofthemember) || !isset($Flatno) || !isset($Sharecertificateno) || !isset($Sharecertificatefrom) || !isset($Sharecertificateto) || !isset($Memberregisterno) )
						{
							$result = '<p>Required Column Names Not Found. Cant Proceed Further......</p>';
							$errormsg=" Column names does not match";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							return $result;
						}
						
					}
				 	else
				   	{
				   		// $test = in_array($row[$UnitNo], array());
				   		
				   		// if(!in_array($row[$UnitNo], $array))
				   		// {
				   		// print_r($row[$Memberregisterno]);
				   		// die();

				   			$errormsg = '';

				   			if($row[$Nameofthemember] == '')
				   			{
				   				$errormsg=" Nameofthemember does not exist";
					   			$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
								$isImportSuccess = false;
				   			}else
				   			{
				   				$nameofthemember = $row[$Nameofthemember];
				   			}

					   		if($row[$Flatno] == '')
					   		{
					   			$errormsg=" Flat no does not exist";
					   			$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
								$isImportSuccess = false;

					   		}else
					   		{
					   			$flatno = $row[$Flatno];					   		
							}

							if($row[$Sharecertificateno] == '')
							{
								$errormsg=" Share certificate no does not exist";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
								$isImportSuccess = false;

							}else
							{
								$sharecertificateno = $row[$Sharecertificateno];
							}


					   		if($row[$Sharecertificatefrom] == '')
					   		{

					   			$errormsg=" Share Certificate From No does not exist";
					   			$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
								$isImportSuccess = false;

					   		}else
					   		{
					   			$sharecertificatefrom = $row[$Sharecertificatefrom];
					   		}
							
							if($row[$Sharecertificateto] == '')
					   		{

					   			$errormsg=" Share Certificate To No does not exist";
					   			$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
								$isImportSuccess = false;

					   		}else
					   		{
					   			$sharecertificateto = $row[$Sharecertificateto];
					   		}
							
							if($row[$Memberregisterno] == '')
							{
								$errormsg="Member register no does not exist";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
								$isImportSuccess = false;

							}else
							{
								$memberregisterno = $row[$Memberregisterno];
							}


							if($isImportSuccess == false)
							{
								$errormsg = "Data not Inserted";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
								
							}else
							{
								$update = "UPDATE `unit` SET `share_certificate`='".$sharecertificateno."', `share_certificate_from`= '".$sharecertificatefrom."', `share_certificate_to`= '".$sharecertificateto."', `member_register_no`= '".$memberregisterno."' WHERE `unit_no`= '".$flatno."' ";
								$query = $this->m_dbConn->update($update);

								$errormsg = "Name of the member : " .$nameofthemember." ,<br> Flatno : ".$flatno." ,<br> Share certificate no: ".$Sharecertificateno." ,<br> Share Certificate From : ".$sharecertificatefrom." ,<br> Share Certificate To : ".$sharecertificateto." ,<br> Member register no : ".$memberregisterno." <br> Added Successfully ";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"I");
				   			}	
					}
				}
			}
	}
}
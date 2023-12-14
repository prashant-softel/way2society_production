<?php
include_once("defaults.class.php");
include_once("utility.class.php");
include_once("billmaster.class.php");
include_once("dbconst.class.php");
set_time_limit(0);
ignore_user_abort(1);
class tarrif_import 
{
	public $m_dbConn;
	public $m_dbConnRoot;
	private $obj_default;
	
	private $obj_utility;
	private $obj_billmaster;
	public $actionPage = "../import_tariff.php?periodid=0";
	public $errorLog;
	public $errorfile_name;	
	private $CategoryIDs = array();
  	private $CategoryName = array();
	
	private $UnitMappingIDs = array();
  	private $UnitMappingNo = array();
	private $LedgerIDs = array();
  	private $LedgerName = array();
	function __construct($dbConnRoot, $dbConn)
	{
		$this->m_dbConnRoot = $dbConnRoot;
		$this->m_dbConn = $dbConn;
		$this->obj_default = new defaults($this->m_dbConn);
		$this->obj_utility = new utility($this->m_dbConn);
		$this->obj_billmaster = new billmaster($this->m_dbConn);		
	}
	
	public function DownloadCSV($IsRequestFromInvoice = false)
	{
		//$getledgers="SELECT a.`category_id`, l.`ledger_name` FROM `ledger` l, `account_category` a where a.`category_name`='Contributions from members' and a.category_id=l.categoryid and l.show_in_bill=1";
		$getledgers="SELECT a.`category_id`, l.`ledger_name` FROM `ledger` l, `account_category` a where a.`category_id`=".$_SESSION['default_contribution_from_member']." and a.category_id=l.categoryid and l.show_in_bill=1";
		
		if($IsRequestFromInvoice == true)
		{
			$NoteType = $_POST['NoteType'];
			
			if($NoteType == CREDIT_NOTE || $NoteType == DEBIT_NOTE)
			{
				$j=5;
				$ledger=array('FCode','Date','Bill_No','Bill_Type','Is_Taxable');	
			}
			else
			{
				$j=3;
				$ledger=array('FCode','InvoiceDate','InvoiceNo');	
			}
		}
		else
		{
			$j=3;
			$get_all = "SELECT w.wing ,u.unit_no, m.owner_name FROM `wing` w, `unit` u, `member_main` m, `society` s where s.society_id='".$_SESSION['society_id']."' and m.unit=u.unit_id and u.wing_id = w.wing_id  and m.ownership_status=1 order by u.sort_order";		
			$get_all1 = $this->m_dbConn->select($get_all);
			$ledger=array('WCode','FCode','OwnerName');
		}
		//echo wjd
		$getledgers1=$this->m_dbConn->select($getledgers);
		
		
		for($i=0;$i<sizeof($getledgers1);$i++)
		{
			$ledger[$j]=$getledgers1[$i]['ledger_name'];
			$j++;
		}
		header('Content-Type: text/csv; charset=utf-8');
		
		if($IsRequestFromInvoice == true)
		{
			
			$ledger[$j] = 'CGST';
			$ledger[$j+1] = 'SGST';
			$ledger[$j+2] = 'Note';
			
			if($NoteType == CREDIT_NOTE)
			{
				header('Content-Disposition: attachment; filename=Credit_Note.csv');
			}
			else if($NoteType == DEBIT_NOTE)
			{
				header('Content-Disposition: attachment; filename=Debit_Note.csv');				
			}
			else
			{
				header('Content-Disposition: attachment; filename=Invoice.csv');
			}

		}
		else
		{
			header('Content-Disposition: attachment; filename=Tariff.csv');
		}
		
    	
		ob_end_clean();
		$output = fopen("php://output","w");		
    	fputcsv($output, $ledger); 
		
		foreach($get_all1 as $value)
		{
			fputcsv($output, $value);
		}
		fclose($output);
		exit();		
		//$result="Done.";
		//return $result;
	}
	
	public function CSVTarrifImport()
	{
		date_default_timezone_set('Asia/Kolkata');		
		$this->errorfile_name = '../process/import_log/tariff_import_errorlog_'.date("d.m.Y").'_'.rand().'.html';
		$this->errorLog = $this->errorfile_name;
		
		$errorfile = fopen($this->errorfile_name, "a");
		if(isset($_POST["Import"]))
		{
			//echo 'Inside CSVTariffImport';						
			if(isset($_FILES)) //&& $_FILES['upload_files']['error'] == 0)
			{
				 $result = "0";				
				 $ext = pathinfo($_FILES['upload_files']['name'][0], PATHINFO_EXTENSION);
				 //$fileName = "files/" . $dateTimeNow. ".csv";
				 $tempName = $_FILES['upload_files']['tmp_name'][0];
				 /*
				 $original_file_name='BuildingID.csv';
				 //echo $_FILES['file'] ['name'];
				 if(($_FILES['file'] ['name']) != "$original_file_name") {
					  //exit("Does not match");
					  $result = '<p>File Name Does Not Match(only BuildingID.csv file accepted)...</p>';
					  
					}
				 else 
				 */
				
				if($ext <> '' && $ext <> 'csv')
				{	
					$result = '<p>Invalid file format selected. Expected csv file format</p>';					
				}
				else
				{
					//if ( move_uploaded_file ($_FILES['file'] ['tmp_name'], $fileName)  )
					
					if (isset($_FILES['upload_files']['error'][0]) || is_array($_FILES['upload_files']['error'][0]))
					{  
						$result = '<p> Data Uploading Process Started <' . $this->getDateTime() . '> </p>';
						
						$method = $_POST['methodofimport'];
						//$lifetime = $_POST['lifetime'];
						//echo "Lifetime: ".$lifetime;												
						//echo "Check: " .PHP_MAX_DATE;
						//die();
						if($method=='Way2Society')
						{
							$result .= $this->UploadData_W2S($tempName,$errorfile);
						}
						else if($method=='Custom')
						{
							$result .= $this->UploadData_Custom($tempName,$errorfile);
						}						
						$result .= '<p> Data Uploading Process Complete <' . $this->getDateTime() . '> </p>';
					}
					else
					{ 
						echo $_FILES['file'] ['error'];
						switch ($_FILES['file'] ['error'])
						{
							case 1:
								   echo '<p> The file is bigger than this PHP installation allows</p>';
								   $result = '<p> The file is bigger than this PHP installation allows</p>';
								   break;
							case 2:
								   echo '<p> The file is bigger than this form allows</p>';
								   $result = '<p> The file is bigger than this form allows</p>';
								   break;
							case 3:
								   echo '<p> Only part of the file was uploaded</p>';
								   $result = '<p> Only part of the file was uploaded</p>';
								   break;
							case 4:
								   echo '<p> No file was uploaded</p>';
								   $result = '<p> No file was uploaded</p>';
								   break;
						}
					} 
				}
			}			
			else if(isset($_FILES['file']) && $_FILES['file']['error'] <> 0)
			{
				$errorCode = $_FILES['file']['error']; 				
				switch ($errorCode)
				{
					case 1:
						   //echo '<p> The file is bigger than this PHP installation allows</p>';
						   $result = '<p> The file is bigger than this PHP installation allows</p>';
						   break;
					case 2:
						   //echo '<p> The file is bigger than this form allows</p>';
						   $result = '<p> The file is bigger than this form allows</p>';
						   break;
					case 3:
						   //echo '<p> Only part of the file was uploaded</p>';
						   $result = '<p> Only part of the file was uploaded</p>';
						   break;
					case 4:
						   //echo '<p> No file was uploaded</p>';
						   $result = '<p> No file was uploaded</p>';
						   break;
				}
			}
			//echo '<body onload="parent.doneloading(\''.$result.'\')"></body>'; 			
			return $result;			
		}
	}
	
	public function UploadData_W2S($fileName,$errorfile)
	{
		$file = fopen($fileName,"r");		
		$errormsg="[Importing Tarrif]";
		$this->obj_utility->logGenerator($errorfile,'start',$errormsg);
		$isImportSuccess = false;
		//$sql00="select tarrif_flag from `import_history` where society_id='".$_SESSION['society_id']."'";
		//$res01=$this->m_dbConn->select($sql00);
		$errormsg = "";	
		$msg = "";	
		$res01=0;
		$exists = array();
		$counterE = 0;
		$value = 0;
		$totalCount = 0;
		$ledgerblank = "";
		$bill_type = $_POST['bill_method'];
		$this -> FetchUnitMappingKey();
		$this -> FetchLedgerKey();
		$this -> FetchCategoryKey();
		$categoryname = "";
		$cat_id = $_SESSION['default_contribution_from_member'];
		
		
		if(in_array($cat_id,$this->CategoryIDs))
		{
			$catIndex = array_search($cat_id,$this->CategoryIDs);
			$categoryname = $this->CategoryName[$catIndex];
		}
		$legder = array();
		try
		{
			if($res01==0)
			{			
				while (($row = fgetcsv($file)) !== FALSE)
				{
					$ledgerblank = "";
						$rowCount++;
						$totalCount = $rowCount;					
						if($rowCount == 1)
						{						
							$FCode=array_search(FCode,$row,true);
							$WCode=array_search(WCode,$row,true);
							$BCode = array_search(BCode,$row,true);
							$OwnerName = array_search(OwnerName,$row,true);
							
							$start_cnt = 2;
							
							if($OwnerName !== false)
							{
								
								$start_cnt = 3;
							}
							if($BCode !== false)
							{
								
								$start_cnt = 4;
							}
							$j = 0;
							
							for($i=$start_cnt; $i < sizeof($row);$i++)
							{		
								if($row[$i] <> '')
								{
								$legder[$j] = trim($row[$i]);	
								$legder_no[$j]=array_search($legder[$j],$row,true);							
								$j++;
								}
							}
						
						}
						else
						{	
							$UnitNo=$row[$FCode];
							$WingCode = $row[$WCode];
							$unitflat = $WingCode ."-". $UnitNo;
						
							$errormsg = "";
							$successmsg = "";
							$successmsg = "<br>WCode : " .$WingCode;	
							$successmsg .= "<br>FCode : " .$UnitNo;
							$msg = "<br>WCode : " .$WingCode;		
							$msg .= "<br>FCode : " .$UnitNo ."<br>";	
							
							if(in_array($unitflat,$this->UnitMappingNo))
							{
								// here We set opening balance for unit is exits and skip insert query because unit already in created	
											
								$unitIndex = array_search($unitflat,$this->UnitMappingNo);
								$UnitID = $this->UnitMappingIDs[$unitIndex];
								$legder_rate = array();		
											
								for($i=0; $i < sizeof($legder); $i++)
								{
									$legder_rate[$i] = $row[$legder_no[$i]];
								}
								//var_dump($legder);
								if($UnitID <> '')
								{
									for($k=0;$k<sizeof($legder);$k++)
									{
										 if(in_array($legder[$k],$this->LedgerName))
											{
												
												$ledgerIndex = array_search($legder[$k],$this->LedgerName);			
												echo '<br>$UnitID : '.$UnitID . ' $ledger Name : ' .$legder[$k] ;
												$ledgerid = $this->LedgerIDs[$ledgerIndex];
	
												 if($ledgerid != 0)
												{
													
													$bf=$_POST['bill_for'];
													$period = $bf;
													$start_period = $bf;
													$end_period = $bf;
													
													$lifetime = $_POST['period'];											
													if($lifetime == 'Lifetime')
													{
														$end_period = 0;
													}
														
													$result = $this->obj_billmaster->update_billmaster($UnitID, $ledgerid, $legder_rate[$k], $period, $start_period, $end_period, $bill_type);	
													//var_dump($UnitID . " " . $HeadID . " " . " ". $legder_rate[$k] . " " . $period . " " . $start_period . " ". $end_period . " " . $bill_type);
												
																																	
												}
											}
										else
										{
										$create = $_POST['create_l'];
											if($create == 'on')
											{														
												$category_id= $_SESSION['default_contribution_from_member'];
													$insert_if_not_found="insert into `ledger`(society_id,categoryid,ledger_name,show_in_bill,taxable,sale,purchase,income,expense,payment,receipt,opening_type,opening_balance,opening_date) values('".$_SESSION['society_id']."','".$category_id."','".$legder[$k]."',1,0,1,0,1,0,0,1,0,0.00,'".$_SESSION['default_year_start_date']."')";
													$legder_insert=$this->m_dbConn->insert($insert_if_not_found);

													$this->LedgerName[] = $legder[$k]; // Add Recently Added ledger name in Ledger Name array Collection
													$this->LedgerIDs[] = $legder_insert; // Add Recently Added ledger id in Ledger id array Collection

											
													$successmsg .= " [Created new legder &lt; ".$legder[$k]." &gt;]";
													$search_account_head="select id from `ledger` where ledger_name='".$legder[$k]."'"; 
													//in category &lt;".$categoryname."&gt;";
													$AccountHead=$this->m_dbConn->select($search_account_head); 
													$HeadID=$AccountHead[0]['id'];
													 if($HeadID != 0)
															{
																
																$bf=$_POST['bill_for'];
																$period = $bf;
																$start_period = $bf;
																$end_period = $bf;
																
																$lifetime = $_POST['period'];											
																if($lifetime == 'Lifetime')
																{
																	$end_period = 0;
																}
																	
																$result = $this->obj_billmaster->update_billmaster($UnitID, $HeadID, $legder_rate[$k], $period, $start_period, $end_period, $bill_type);	
																//var_dump($UnitID . " " . $HeadID . " " . " ". $legder_rate[$k] . " " . $period . " " . $start_period . " ". $end_period . " " . $bill_type);
															
																																				
															}
															
											}
											else
											{
												 $errormsg .= "<br>Please check ledger &lt;".$legder[$k]."&gt; and/or category &lt;".$categoryname."&gt; exist or tick checkbox 'Create new ledger' to create ledgers that were not found.";
												
											}
										}								
										if($k == 0)
										{
											$successmsg .= "<br> Ledger : ";
										}
										if($legder_rate[$k] <> '')
										{
										 $successmsg .= " &lt;".$legder[$k]."&gt; Amount :  &lt;".$legder_rate[$k]."&gt; ";																
										 $ledgerblank .= $legder[$k] .",";
										}
										
												
												
																			
									}
									$isImportSuccess = true;
									$bSuccess = true;									
											
								}					
							}
							else
							{
								$errormsg .= "FCode &lt;".$UnitNo."&gt; or WCode &lt;".$WingCode."&gt; Not Found";
								
							}
						}
						if($rowCount <> 1)
						{
						if($errormsg <> '')
						{
							$errormsg .= "<br>Not Imported Successfully";
							$counterE++;
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
						}
						else
						{
							if($ledgerblank <> '')
							{
								$successmsg .= "<br>Imported Successfully";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$successmsg,"I");	
							}
							else
							{
								$msg .= "Amount Not Present at any Ledger";
								$counterE++;
								$msg .= "<br>Not Imported Successfully";
								
								$this->obj_utility->logGenerator($errorfile,$rowCount,$msg,"E");	
							}
						}
						}
					}
							
				if($bSuccess)
				{
					$update_import_history="update `import_history` set tarrif_flag=1 where society_id='".$_SESSION['society_id']."'";
					$data23=$this->m_dbConn->update($update_import_history);
				//	$successmsg .= "<br> Row Imported Successfully";	
				}
				else
				{
					$errormsg="Tarrif details not imported.";
					$this->obj_utility->logGenerator($errorfile,'Error',$errormsg,"E");	
				}			
			}
		}
		catch(Exception $exp)
		{
			echo $exp->getMessage();
		}
		/*$errormsg = "Number of errors: <font color='#FF0000'>".$counterE."</font><br>";
		$this->obj_utility->logGenerator($errorfile,'End',$errormsg);
		$errormsg = "Number of inserts: <font color='#006600'>".$counterI."</font><br>";
		$this->obj_utility->logGenerator($errorfile,'End',$errormsg);
		$errormsg = "Number of updates: <font color='#FFCC00'>".$counterU."</font><br>";
		$this->obj_utility->logGenerator($errorfile,'End',$errormsg);*/
	$totalCount = $totalCount - 1;
	//var_dump($ledgerblank);
		
	 $errormsg = "<br><br><b>Number of Rows : ".$totalCount."</b>";
	 
	 $errormsg .= "<br><b>Number of Rows Imported : ".($totalCount - $counterE)."</b>";
	 $errormsg .= "<br><b>Number of Rows Not Imported : ".$counterE."</b>";
	 $this->obj_utility->logGenerator($errorfile,'',$errormsg);

		$errormsg="<br><br>[End of Tarrif]";
		$this->obj_utility->logGenerator($errorfile,'End',$errormsg);	
	}
	
	public function UploadData_Custom($fileName,$errorfile)
	{
		$file = fopen($fileName,"r");
		$errormsg="[Importing Tarrif]";
		$this->obj_utility->logGenerator($errorfile,'start',$errormsg);
		$isImportSuccess = false;
		$sql00="select tarrif_flag from `import_history` where society_id='".$_SESSION['society_id']."'";
		$res01=$this->m_dbConn->select($sql00);
		if($res01[0]['tarrif_flag']==0)
		{
			$aryMain = array();
			
			while (($row = fgetcsv($file)) !== FALSE)
			{
				if($row[0] <> '')
				{
					$rowCount++;
					if($rowCount == 1)
					{
						$WCode=array_search(WCode,$row,true);
						$BCode=array_search(BCode,$row,true);
						$FCode=array_search(FCode,$row,true);
						$Particulars=array_search(Particulars,$row,true);
						$AccountName=array_search(AccountName,$row,true);
						$Rate=array_search(Rate,$row,true);
						$EffectiveDate = array_search(EffectiveDate,$row,true);
						 
						if(!isset($WCode) || !isset($FCode) || !isset($BCode) || !isset($Particulars) || !isset($AccountName) || !isset($Rate) || !isset($EffectiveDate))
						{
							$result = '<p>Column Names Not Found Cant Proceed Further......</p>'.'Go Back';
							$errormsg=" Column names  in file Tarrif not match";
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
							return $result;
							exit(0);
						}
					}
					else
					{	
						$UnitNo=$row[$FCode];
						$wing=$row[$WCode];
						$ledger_name=$row[$Particulars];
						$account_category=$row[$AccountName];
						$AccountHeadAmount=$row[$Rate];
						$EffDate = $row[$EffectiveDate];
						
						$sql="select unit_id from `unit` where unit_no='".$UnitNo."' and society_id='".$_SESSION['society_id']."' ";
						$data=$this->m_dbConn->select($sql);
						$UnitID=$data[0]['unit_id'];
						if($UnitID <> '')
						{
				
						$search_account_head="select id from `ledger` where ledger_name='".$ledger_name."' ";
						$AccountHead=$this->m_dbConn->select($search_account_head);
						if($AccountHead == '')
						{
							$errormsg = "Ledger id not found for ledger  name &lt;" .$ledger_name."&gt  please check if  account name match with ledger name in tarrif  file " ;
							$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");  		
						} 
						
						$HeadID=$AccountHead[0]['id'];
						$bUpdateAmount = true;
						if(!array_key_exists($UnitID, $aryMain))
						{
							$aryMain[$UnitID] = array("date"=>$EffDate, "data"=>array());
						}
						else
						{
							$date1 = date("Y-m-d", strtotime($aryMain[$UnitID]['date'])) . ' ';
							$date2 = date("Y-m-d", strtotime($EffDate)) . ' ';
							$dateDiff = $this->obj_utility->getDateDiff($date1, $date2);
							if($dateDiff <= 0)
							{
								$aryMain[$UnitID]['date'] = $EffDate;
								if($dateDiff <> 0)
								{
									unset($aryMain[$UnitID]['data']);
									$aryMain[$UnitID]['data'] = array();
								}
							}
							else
							{
								$bUpdateAmount = false;
							}
						}
						
						if($bUpdateAmount)
						{
							if($AccountHeadAmount <> 0)
							{
								if(!array_key_exists($aryMain[$UnitID]['data'][$HeadID], $aryMain[$UnitID]['data']))
								{
									$aryMain[$UnitID]['data'][$HeadID] = $AccountHeadAmount;
								}
								else
								{
									$aryMain[$UnitID]['data'][$HeadID] = $AccountHeadAmount;
								}
							}
						}
						
						}
					}
			
					
				}
			}
			
			$bSuccess = false;
			foreach($aryMain as $k=>$v)
			{
				$bHasValues = false;
				$insertStatement = '';
				$iCounter = 1;
				if(sizeof($v['data']) > 0)
				{
					foreach($v['data'] as $head=>$amount)
					{
						$bHasValues = true;
						if($iCounter == 1)
						{
							$insertStatement = ' ("' . $k . '", "' . $_SESSION['login_id'] . '", 0, "' . $head . '", "' . $amount . '")';
						}
						else
						{
							$insertStatement .= ',("' . $k . '", "' . $_SESSION['login_id'] . '", 0, "' . $head . '", "' . $amount . '")';
						}
						$iCounter++;
						
					}
					$insert_unitbillmaster="insert into `unitbillmaster`(UnitID,CreatedBy,LatestChangeID,AccountHeadID,AccountHeadAmount) values " . $insertStatement ;
					
					//$this->obj_utility->logGenerator($errorfile,$rowCount,$insert_unitbillmaster,"W");  
					
					$data1=$this->m_dbConn->insert($insert_unitbillmaster);
					$isImportSuccess = true;
					$bSuccess = true;
				}
		
			
			}
			
			if($bSuccess)
			{
				$update_import_history="update `import_history` set tarrif_flag=1 where society_id='".$_SESSION['society_id']."'";
				$data23=$this->m_dbConn->update($update_import_history);						
			}
			else
			{
				$errormsg="Tarrif details not imported";
				$this->obj_utility->logGenerator($errorfile,'Error',$errormsg,"E");	
			}	
			
		}
		
		$errormsg="[End of  Tarrif]";
		$this->obj_utility->logGenerator($errorfile,'End',$errormsg);
	}
	
	function getDateTime()
	{
		$dateTime = new DateTime();
		$dateTimeNow = $dateTime->format('Y-m-d H:i:s');
		return $dateTimeNow;
	}
	
	public function combobox($query, $id, $defaultText = 'Please Select', $defaultValue = '')
	{
		$str = '';
		
		/*if($defaultText != '')
		{
			$str .= "<option value='" . $defaultValue . "'>" . $defaultText . "</option>";
		}*/
		//echo "$query";
		$data = $this->m_dbConn->select($query);
		//print_r($data);
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
	
public function FetchUnitMappingKey()
  {
	 $sql="SELECT concat_ws('-',w.wing,u.unit_no) as unit_no,u.unit_id,w.wing FROM `wing` w, `unit` u, `society` s where s.society_id='".$_SESSION['society_id']."' and u.wing_id = w.wing_id";
	 $Result=$this->m_dbConn->select($sql);
	 //var_dump($Result); 
	 $this->UnitMappingNo = array_column($Result, 'unit_no');
	 
	 $this->UnitMappingIDs = array_column($Result, 'unit_id');
  }
  public function FetchLedgerKey()
  {
	 $sql="select id,ledger_name from `ledger` where categoryid = ".$_SESSION['default_contribution_from_member']."";
	 $Result=$this->m_dbConn->select($sql);
	 //var_dump($Result); 
	 $this->LedgerIDs = array_column($Result, 'id');
	 $this->LedgerName = array_column($Result, 'ledger_name');
  }
  public function FetchCategoryKey()
  {
	 $sql="SELECT category_id,category_name FROM `account_category`";
	 $Result=$this->m_dbConn->select($sql);
	 //var_dump($Result); 
	 $this->CategoryIDs = array_column($Result, 'category_id');
	 $this->CategoryName = array_column($Result, 'category_name');
  }
  
}

?>
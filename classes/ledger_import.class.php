  <?php
  //include_once("include/dbop.class.php");
  include_once("register.class.php");
  include_once("utility.class.php");
  include_once("dbconst.class.php");
  include_once("include/fetch_data.php");
  class ledger_import 
  {
	  
	  public $allColumns = array('GroupId', 'Category', 'SubCategory',
				  'Description', 'OpeningBalance', 'OpeningType', 'Remark',
				  'TariffTag', 'TaxFlag', 'Tax_Below_Threshold_Also', 'GSTIN', 'PAN', );
  
	  public $ci = array();
  
	  const GROUP_ID = 0;
	  const CATEGORY = 1;
	  const SUB_CATEGORY = 2;
	  const DESCRIPTION = 3;
	  const OPENING_BALANCE = 4;
	  const OPENING_TYPE = 5;
	  const REMARK = 6;
	  const TARIFF_FLAG = 7;
	  const TAX_FLAG = 8;
	  const TAX_NO_THRESHOLD = 9;
	  const GSTIN = 12;
	  const PAN = 13;
  
	  public $m_dbConn;
	  public $m_dbConnRoot;
	  public $obj_register;
	  public $obj_utility;
	  public $errorfile_name;
	  public $errorLog;
	  public $actionPage = '../import_ledger.php';
	  public $csv;
	  public $debug_trace;
	  
	  function __construct($dbConnRoot, $dbConn)
	  {
		  $this->m_dbConnRoot = $dbConnRoot;
		  $this->m_dbConn = $dbConn;
		  $this->obj_register = new regiser($this->m_dbConn);
		  $this->obj_utility = new utility($this->m_dbConn);
		  $this->debug_trace = 0;
		  //var_dump($this->bank_cash_array);
		  $this->obj_fetch = new FetchData($this->m_dbConn);

		$a = $this->obj_fetch->GetSocietyDetails($_SESSION['society_id']);
	  }
	  
	  public function CSVLedgerImport()
	  {
		  if(isset($_POST["Import"]))
		  {
			  //echo 'Inside CSVUnitImport';
			  if(isset($_FILES['file']) && $_FILES['file']['error'] == 0)
			  {
				  $result = "0";
				   $ext = pathinfo($_FILES['file'] ['name'], PATHINFO_EXTENSION);
				  //$fileName = "files/" . $dateTimeNow. ".csv";
				   $tempName = $_FILES['file'] ['tmp_name'];
				  /*
				  $original_file_name='AccountMaster.csv';
				  //echo $_FILES['file'] ['name'];
				  if(($_FILES['file'] ['name']) != "$original_file_name") {
						//exit("Does not match");
						$result = '<p>File Name Does Not Match(only AccountMaster.csv file accepted)...</p>';
						
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
					  if (isset($_FILES['file']['error']) || is_array($_FILES['file']['error']))
					  {  
						  $result = '<p> Ledger Data Uploading Process Started <' . $this->obj_utility->getDateTime() . '> </p>';
						  
						  $result .= $this->UploadData($tempName);
						  
						  $result .= '<p> Ledger Data Uploading Process Complete <' . $this->obj_utility->getDateTime() . '> </p>';
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
	  
	  public function UploadData($fileName,$errorfile)
	  {
	  	  $account_type = 1;
		  $transactionType = TRANSACTION_CREDIT;
					
		  $file = fopen($fileName,"r");
		  $errormsg="[Importing Accountmaster]";
		  $this->obj_utility->logGenerator($errorfile,'start',$errormsg);
		  $isImportSuccess = true;
		  $sql00="select ledger_flag from `import_history` where society_id='".$_SESSION['society_id']."'";
		  $res01=$this->m_dbConn->select($sql00);
		  if($res01[0]['ledger_flag']==0)
		  {
			$Date = $this->obj_utility->get_begining_date_minus_one($_POST['Period']);
 
		  	$result = '';
		  
		    while (($row = fgetcsv($file)) !== FALSE)
		    {
			  
			  
			  if($row[0] <> '')
			  {
				  $rowCount++;
				  if($rowCount == 1)
				  {
					  $GroupId=array_search(GroupId,$row,true);
					  $Category=array_search(Category,$row,true);
					  $SubCategory=array_search(SubCategory,$row,true);
					  $Description=array_search(Description,$row,true);
					  $FCode=array_search(FCode,$row,true);
					  $OpeningType=array_search(OpeningType,$row,true);
					  $OpeningBalance=array_search(OpeningBalance,$row,true);
					  $TaxFlag=array_search(TaxFlag,$row,true);
					  $Remark=array_search(Remark,$row,true);
					  //$SubCategory=array_search(SubCategory,$row,true);
					  $TariffTag=array_search(TariffTag,$row,true);
						  
					  if(!isset($GroupId) || !isset($Category)  || !isset($SubCategory) || !isset($Description) || !isset($FCode) || !isset($OpeningType) || !isset($OpeningBalance) || !isset($TaxFlag) || !isset($Remark) || !isset($SubCategory))
					  {
						  $result = '<p>Column Names Not Found Cant Proceed Further......</p>'.'Go Back';
						  $errormsg=" Column names in file Accountmaster not match";
						  $this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
						  return $result;
						  exit(0);
					  }
				  
					  
				  }
				 else
				 {
					  $groupname=$row[$GroupId];
					  $category_name=$row[$Category];
					  $sub_category_name=$row[$SubCategory];
					  //$sub_category_name=strtolower($row[$SubCategory]);
					  $unit_no=$row[$FCode];
					  $opening_type=$row[$OpeningType];
					  $opening_balance=$row[$OpeningBalance];
					  $taxable=$row[$TaxFlag];
					  $show_in_bill=$row[$TariffTag];
					  $note=$row[$Remark];
					  $society_id = $_SESSION['society_id'];
					  $sale=0;
					  $purchase=0;
					  $income=0;
					  $expense=0;
					  $payment=0;
					  $receipt=0;
					  $is_ledger_unit=0;
					  if($unit_no=='')
					  {
						  $ledger_name=$row[$Description];
						  
					  }
					  else
					  {
						  $is_ledger_unit=1;
					   	  $ledger_name=$row[$FCode];	
					  }
					  
					  
					  if(strtolower($show_in_bill)=='y')
					  {
						  $showinbill_flag=1;
						  
					  }
					  else
					  {
						  $showinbill_flag=0;
					  }
						  
					  if($ledger_name <> 0 || $ledger_name <> '')
					  { 

			  			$category_id = $this->obj_utility->GetCategory_ID($category_name, $sub_category_name, $groupname, 0);

						
					  if($is_ledger_unit==1)
					  {	  
					  $search_ledger = "select * from  ledger where ledger_name='".$ledger_name."' and society_id='".$_SESSION['society_id']."'";
							 $search=$this->m_dbConn->select($search_ledger);
							 
							 if($search <> '')
							 {
							  
								$errormsg="ledger name <" .$ledger_name. ">  already exits in ledger table";
								$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg);
								$del1="delete  from `ledger` where ledger_name='".$ledger_name."' and society_id='".$_SESSION['society_id']."'";
								$del01=$this->m_dbConn->delete($del1);
								$del2="delete  from `assetregister` where LedgerID='".$search[0]['id']."'  and Is_Opening_Balance=1 ";
								$del02=$this->m_dbConn->delete($del2);
								$del3="delete  from `liabilityregister` where LedgerID='".$search[0]['id']."' and Is_Opening_Balance=1";
								$del03=$this->m_dbConn->delete($del3);
								$del4="delete  from `bankregister` where LedgerID='".$search[0]['id']."' and Is_Opening_Balance=1";
								$del04=$this->m_dbConn->delete($del4);
							  }
							 
					  }
					  if(strcasecmp($opening_type,"CREDIT")==0)
					  {
						  $account_type=1;
						  $transactionType = TRANSACTION_CREDIT;
					  }
					  else
					  {
						  $account_type=2;
						  $transactionType = TRANSACTION_DEBIT;
					  }
					  //$aryCategoryParent = $this->obj_utility->getParentOfCategory($category_id);
					  if(strcasecmp($groupname,'Liability')==0)
					  {
						  
						  $insert_ledger="insert into `ledger`(society_id,categoryid,show_in_bill,ledger_name,taxable,sale,purchase,income,expense,payment,receipt,opening_type,opening_balance,note,`opening_date`) values('$society_id','$category_id','$showinbill_flag','$ledger_name',0,0,0,0,1,1,1,'$account_type','".abs($opening_balance)."','$note','".getDBFormatDate($Date)."')";
						  $errormsg= "Ledger Name: &lt;".$ledger_name."&gt; :: Type: &lt; Liability &gt;";
					  }
					  
					  else if(strcasecmp($groupname,'Asset')==0)
					  {
						  $paymentFlag = 0;
						  if($category_id == $_SESSION['default_bank_account'] || $category_id == $_SESSION['default_cash_account'])
							  {
								  $paymentFlag = 1;
							  }
		  
						  $insert_ledger="insert into `ledger`(society_id,categoryid,show_in_bill,ledger_name,taxable,sale,purchase,income,expense,payment,receipt,opening_type,opening_balance,note,`opening_date`) values('$society_id','$category_id','$showinbill_flag','$ledger_name',0,1,1,1,0,'$paymentFlag',1,'$account_type','".abs($opening_balance)."','$note','".getDBFormatDate($Date)."')";
						  $errormsg= "Ledger Name: &lt;".$ledger_name."&gt; :: Type: &lt; Asset &gt;";
					  }
					  
					  else if(strcasecmp($groupname,'Income')==0)
					  {
						  $insert_ledger="insert into `ledger`(society_id,categoryid,show_in_bill,ledger_name,taxable,sale,purchase,income,expense,payment,receipt,opening_type,opening_balance,note,`opening_date`) values('$society_id','$category_id','$showinbill_flag','$ledger_name',0,1,0,1,0,0,1,'$account_type','".abs($opening_balance)."','$note','".getDBFormatDate($Date)."')";
						  $errormsg= "Ledger Name: &lt;".$ledger_name."&gt; :: Type: &lt; Income &gt;";
					  }
					  else if(strcasecmp($groupname,'Expense')==0)
					  {
						  
						  $insert_ledger="insert into `ledger`(society_id,categoryid,show_in_bill,ledger_name,taxable,sale,purchase,income,expense,payment,receipt,opening_type,opening_balance,note,`opening_date`) values('$society_id','$category_id','$showinbill_flag','$ledger_name',0,0,0,0,1,1,0,'$account_type','".abs($opening_balance)."','$note','".getDBFormatDate($Date)."')";
						  $errormsg= "Ledger Name: &lt;".$ledger_name."&gt; :: Type: &lt; Expense &gt;";
					  }
					  $NewLedgerID=$this->m_dbConn->insert($insert_ledger);
					  if($NewLedgerID > 0)
					  {
						  $this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"I");
						  $aryParent = $this->obj_utility->getParentOfLedger($NewLedgerID);
						  
						  
						  if($aryParent['group'] == LIABILITY)
						  {
							  $insertLiability = $this->obj_register->SetLiabilityRegister(getDBFormatDate($Date), $NewLedgerID, 0, 0, $transactionType, abs($opening_balance), 1);
						  }
						  else if($aryParent['group'] == ASSET)
						  {
							  if($category_id == $_SESSION['default_bank_account'] || $category_id == $_SESSION['default_cash_account'])
	
							  //else if(strcasecmp($aryParent['category_name'], "Bank Balances") == 0 || strcasecmp($aryParent['category_name'],"Cash Balance") == 0)
							  {
								  $insertBank = $this->obj_register->SetBankRegister(getDBFormatDate($Date), $NewLedgerID, 0, 0, TRANSACTION_RECEIVED_AMOUNT, abs($opening_balance), 0, 0, 1);
								  //$errormsg=implode(' || ',$row);
								  //$this->obj_utility->logGenerator($errorfile,"BAnk",$errormsg,"I");
								  $insertBankMaster = $insert_query="insert into bank_master (`BankID`, `BankName`) values ('" . $NewLedgerID . "', '".$ledger_name."')";
								  $sqlInsertResult = $this->m_dbConn->insert($insertBankMaster);
							  }
							  else
							  {
								  $insertAsset = $this->obj_register->SetAssetRegister(getDBFormatDate($Date), $NewLedgerID, 0, 0, $transactionType, abs($opening_balance), 1);
							  }
						  }				
						  else if($aryParent['group'] == INCOME)
						  {
								  $insertIncome = $this->obj_register->SetIncomeRegister($NewLedgerID, getDBFormatDate($Date), 0, 0, $transactionType, abs($opening_balance), 1);
						  }				
						  else if($aryParent['group'] == EXPENSE)
						  {
								  $insertIncome = $this->obj_register->SetExpenseRegister($NewLedgerID, getDBFormatDate($Date), 0, 0, $transactionType, abs($opening_balance), 1);
						  }				
					  }
					  else
					  {
						  $isImportSuccess = false;
						  $this->obj_utility->logGenerator($errorfile,$rowCount,"Error importing " . $errormsg, "E");
					  }
				  }//if end
				  else
				  {
					  $errormsg="Ledger name blank in Fcode or Description Column";
					  $this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg);	
				  }
			  }//else end
			  
		  	}//if end
		  
		  }//while end
	  
		  if($isImportSuccess)
		  {
			  $update_import_history="update `import_history` set ledger_flag=1 where society_id='".$_SESSION['society_id']."'";							
			  $res123=$this->m_dbConn->update($update_import_history);
		  }
		  else
		  {
			  $errormsg="ledger details not imported";
			  $this->obj_utility->logGenerator($errorfile,'Error',$errormsg,"E");	
		  }	
	  
		  }//main if end
		  $errormsg="[End of  AccountMaster]";
		  $this->obj_utility->logGenerator($errorfile,'End',$errormsg);
		  return $result;
	  }
///////////////////////////////////////////////////////
//**	  	UploadDataManually
///////////////////////////////////////////////////////
	  public function UploadDataManually($indexes, $array, $new_ledger_flag = '',$import_opening_bal = '')
	  {	 
		try
		{
		  if ($new_ledger_flag != 'on')
		  {
			  $new_ledger_flag = 'off';
		  }
		  if ($import_opening_bal != 'on')
		  {
			  $import_opening_bal = 'off';
		  }
	  
		  $society_id = $_SESSION['society_id'];
	  	  $Date = $this->obj_utility->get_begining_date_minus_one($_SESSION['default_period']);
		  $Foldername = $this->obj_fetch->objSocietyDetails->sSocietyCode;

			if (!file_exists('../logs/import_log/'.$Foldername)) 
			{
				mkdir('../logs/import_log/'.$Foldername, 0777, true);
			}

		  $this->errorfile_name = '../logs/import_log/'.$Foldername.'/update_ledgers_errorlog_'.date("d.m.Y").'_'.rand().'.html';
		  $this->errorLog = $this->errorfile_name;
		  $errorfile = fopen($this->errorfile_name, "a");
		  $errormsg="[Updating Ledger]";
		  $this->obj_utility->logGenerator($errorfile,'start',$errormsg);
		  $isImportSuccess = true;
  
		  if($this->debug_trace == 1)
		  {
			  echo "<br>Post Ledger Opening Date Period ID : ".$_SESSION['default_period'];
			  echo "<br>Ledger Opening Date is : ".$Date;
		  }
		  
		  
  
		  //Getting the columns to be updated which were checked by the user
		  for ($i = 0; $i < count($this->allColumns); $i++)
		  {
			  if (in_array($i, $indexes))
			  {
				  switch($this->allColumns[$i])
				  {
					  case 'GroupId':
						  $this->ci[ledger_import::GROUP_ID][0] = $i;
						  $this->ci[ledger_import::GROUP_ID][1] = 1;
						  break;
					  case 'Category':
						  $this->ci[ledger_import::CATEGORY][0] = $i;
						  $this->ci[ledger_import::CATEGORY][1] = 1;
						  break;
					  case 'SubCategory':
						  $this->ci[ledger_import::SUB_CATEGORY][0] = $i;
						  $this->ci[ledger_import::SUB_CATEGORY][1] = 1;
						  break;
					  case 'Description':
						  $this->ci[ledger_import::DESCRIPTION][0] = $i;
						  $this->ci[ledger_import::DESCRIPTION][1] = 1;
						  break;
					  case 'OpeningBalance':
						  $this->ci[ledger_import::OPENING_BALANCE][0] = $i;
						  $this->ci[ledger_import::OPENING_BALANCE][1] = 1;
						  break;
					  case 'OpeningType':
						  $this->ci[ledger_import::OPENING_TYPE][0] = $i;
						  $this->ci[ledger_import::OPENING_TYPE][1] = 1;
						  break;
					  case 'Remark':
						  $this->ci[ledger_import::REMARK][0] = $i;
						  $this->ci[ledger_import::REMARK][1] = 1;
						  break;
					  case 'TariffTag':
						  $this->ci[ledger_import::TARIFF_FLAG][0] = $i;
						  $this->ci[ledger_import::TARIFF_FLAG][1] = 1;
						  break;
					  case 'TaxFlag':
						  $this->ci[ledger_import::TAX_FLAG][0] = $i;
						  $this->ci[ledger_import::TAX_FLAG][1] = 1;
						  break;
						  
					  case 'Tax_Below_Threshold_Also':
						  $this->ci[ledger_import::TAX_NO_THRESHOLD][0] = $i;
						  $this->ci[ledger_import::TAX_NO_THRESHOLD][1] = 1;
						  break;
					  case 'GSTIN':
						  $this->ci[ledger_import::GSTIN][0] = $i;
						  $this->ci[ledger_import::GSTIN][1] = 1;
					  case 'PAN':
						  $this->ci[ledger_import::PAN][0] = $i;
						  $this->ci[ledger_import::PAN][1] = 1;
						  break;
				  }
			  }
		  }
  
		  
		  //Assigning the columns to the particular column names
		  for ($i = 0; $i < count($this->allColumns); $i++)
		  {
			  switch($i)
			  {
				  case $this->ci[ledger_import::CATEGORY][0]:
						  $Category=$this->allColumns[ledger_import::CATEGORY];
						  break;
				  case $this->ci[ledger_import::GROUP_ID][0]:
						  $GroupId=$this->allColumns[ledger_import::GROUP_ID];
						  break;
				  case $this->ci[ledger_import::SUB_CATEGORY][0]:
						  $SubCategory=$this->allColumns[ledger_import::SUB_CATEGORY];
						  break;
				  case $this->ci[ledger_import::DESCRIPTION][0]:
						  $Description = $this->allColumns[ledger_import::DESCRIPTION];	
						  break;
				  case $this->ci[ledger_import::OPENING_BALANCE][0]:
						  $OpeningBalance=$this->allColumns[ledger_import::OPENING_BALANCE];
						  break;
				  case $this->ci[ledger_import::OPENING_TYPE][0]:
						  $OpeningType=$this->allColumns[ledger_import::OPENING_TYPE];
						  break;
				  case $this->ci[ledger_import::REMARK][0]:
						  $Remark=$this->allColumns[ledger_import::REMARK];
						  break;
				  case $this->ci[ledger_import::TARIFF_FLAG][0]:
						  $TariffTag=$this->allColumns[ledger_import::TARIFF_FLAG];
						  break;
				  case $this->ci[ledger_import::TAX_FLAG][0]:
						  $TaxFlag=$this->allColumns[ledger_import::TAX_FLAG];
						  break;
				  case $this->ci[ledger_import::TAX_NO_THRESHOLD][0]:
						  $TaxNoThresholdFlag=$this->allColumns[ledger_import::TAX_NO_THRESHOLD];
						  break;
				  case $this->ci[ledger_import::GSTIN][0]:
						  $GSTIN=$this->allColumns[ledger_import::GSTIN];
						  break;
				  case $this->ci[ledger_import::PAN][0]:
						  $PAN=$this->allColumns[ledger_import::PAN];
						  break;
			  }
		  }
		  $rowCount = 0;
		  $import_opening_bal_  = 0;
		  //echo "<BR>import_opening_bal_  : $import_opening_bal_";
		  if($import_opening_bal == "on")
		  {
			  if(($_SESSION['default_year'] == $_SESSION['society_creation_yearid']))
			  {
				  $import_opening_bal_ = 1;
			  }
			  else
			  {
				  $errormsg = "Opening balances cannot be updated as current year is not the opening year.";
				  $this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
				  if($this->debug_trace == 1)
				  {
					  echo "<br>$errormsg";
				  }
	    	  	  return;
			  }
		  }
		  else
		  {
				$errormsg = "Check box to import ledger balance is not checked. Opening balances wont be updated.";						
			  	$this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"W");		  
		  }
		  
		  if($this->debug_trace == 1)
		  {
			  echo "<br>Import New Ledgers : ".$new_ledger_flag;
			  echo "<br>Import opening balance : ".$import_opening_bal;
			  echo "<br>Society_creation_yearID : ".$_SESSION['society_creation_yearid'];
			  echo "<br>Default Society_yearID : ".$_SESSION['default_year'];			  
			  echo "<br>$errormsg";
			  //echo "<br>Can import Ledger Opening balance? ".$import_opening_bal_;
		  }
		  
	  	$this->obj_utility->logGenerator($errorfile,$rowCount,"<BR>Import New Ledgers : $new_ledger_flag ","I");		  
	  	$this->obj_utility->logGenerator($errorfile,$rowCount,"<BR>Import opening balance : .$import_opening_bal","I");		  
		  //Iterating through each and every row and updating the values in the database.
		  for ($i = 1; $i < count($array) - 1; $i++)
		  {
			  $rowCount++;
		  	  $m_TraceDebugInfo = "";
			  if($this->debug_trace == 1)
			  {
				  echo "<br>---------------------------------------------";
			  }
			  $groupname=$array[$i][ledger_import::GROUP_ID];
			  if ($groupname != '') 
			  {
				  $m_TraceDebugInfo .= "Group Name: &lt;". $groupname . "&gt; ";
				  // $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
			  }
  
			  $category_name=$array[$i][ledger_import::CATEGORY];
			  if ($category_name != '') 
			  {
				  $m_TraceDebugInfo .= "Category Name: &lt;". $category_name . "&gt; ";
				  // $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");				
			  }
  
			  //$sub_category_name=strtolower($array[$i][ledger_import::SUB_CATEGORY]);
			  $sub_category_name=$array[$i][ledger_import::SUB_CATEGORY];
			  if ($sub_category_name != '') 
			  {
				  $m_TraceDebugInfo .= "SubCategory Name: &lt;". $sub_category_name . "&gt; ";
				  // $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");				
			  }
  
			  $opening_type=$array[$i][ledger_import::OPENING_TYPE];
			  if ($opening_type != '')
			  {
				  $m_TraceDebugInfo .= "Opening Type: &lt;". $opening_type . "&gt;";
				  // $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");				
			  }
  
			  $opening_balance=$array[$i][ledger_import::OPENING_BALANCE];
			  if ($opening_balance != '')
			  {
				  $m_TraceDebugInfo .= "Opening Balance: &lt;". $opening_balance . "&gt; ";
				  // $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");				
			  }
  
			  $taxable=$array[$i][ledger_import::TAX_FLAG];
			  $taxable_flag = 0;
			  if ($taxable != '')
			  {
				  $m_TraceDebugInfo .= "Tax Flag: &lt;". $taxable . "&gt; ";
				  if ((strtolower($taxable) == 'y') || ($taxable == 1))
				  {
					  $taxable_flag = 1;
				  }
				  // $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");				
			  }

			  $tax_no_threshold=$array[$i][ledger_import::TAX_NO_THRESHOLD];
			  $tax_no_threshold_flag = 0;
			  if ($tax_no_threshold != '')
			  {
				  $m_TraceDebugInfo .= "No Threshold Tax Flag: &lt;". $tax_no_threshold . "&gt; ";
				  if ((strtolower($tax_no_threshold) == 'y') || ($tax_no_threshold == 1))
				  {
					  $tax_no_threshold_flag = 1;
				  }
				  // $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");				
			  }
  
			  $show_in_bill=$array[$i][ledger_import::TARIFF_FLAG];
			  $show_in_bill_flag = 0;
			  if(strtolower($show_in_bill)=='y' || $show_in_bill == 1)
			  {
				  $show_in_bill_flag=1;
			  }
			  
			  $m_TraceDebugInfo .= "Tariff Flag: &lt;". $show_in_bill_flag . "&gt; ";
  
			  $note=$array[$i][ledger_import::REMARK];
			  if ($note != '')
			  {
				  $m_TraceDebugInfo .= "Remark: &lt;". $note . "&gt; ";
				  // $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");				
			  }
  
			  $gstin = $array[$i][ledger_import::GSTIN];
			  if ($gstin != '')
			  {
				  $m_TraceDebugInfo .= "GSTIN: &lt;". $gstin . "&gt; ";
				  // $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");				
			  }
  
			  $pan = $array[$i][ledger_import::PAN];
			  if ($pan != '')
			  {
				  $m_TraceDebugInfo .= "PAN: &lt;". $pan . "&gt; ";
				  // $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");				
			  }
  
			  // $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");				
  
			  $sale=0;
			  $purchase=0;
			  $income=0;
			  $expense=0;
			  $payment=0;
			  $receipt=0;
  
					  
			  $ledger_name=$array[$i][ledger_import::DESCRIPTION];
  
			  if($ledger_name != '')
			  {
			  	$m_TraceDebugInfo .= "Ledger Name: &lt;". $ledger_name . "&gt; ";
			  }
			  // $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
				  if($this->debug_trace == 1)
				  {
					  echo "<BR>$m_TraceDebugInfo<BR>";
				  }
			  $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");				
			  if($this->debug_trace == 1)
			  {
				  echo "<BR>" . $m_TraceDebugInfo;
			  }
			  //Getting the category id for a particular ledger
			  if ($new_ledger_flag == 'on')
			  {
				  $toInsert = 1;
			  }
			  $category_name;
			  $sub_category_name;
			  $groupname;
			  //Pending optimisation : Create map of gr-cat-subcat to catid
			  $category_id_array = $this->obj_utility->GetCategoryDetails($category_name, $sub_category_name, $groupname, $toInsert);

			  $category_id = $category_id_array['category_id'];
			  $group_id = $category_id_array['group_id'];
				  
			  if($category_id > 0)
			  {				 
			  	  $errormsg =  "Subcategory " .$sub_category_name. " of category " .$category_name . " of group " .$groupname. " found. ID=" . $category_id;
				  if($this->debug_trace == 1)
				  {
					  echo "<BR>" . $errormsg ;
				  }
				  $this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"W");
			  }
			  else
			  {
				  $errormsg = "Category : $category_name or Subcategory : $sub_category_name for group : $groupname not found.. ";
				  if($this->debug_trace == 1)
				  {
					  var_dump($category_id_array);
					  echo "<BR>" . $errormsg;
				  }
				  $this->obj_utility->logGenerator($errorfile, $rowCount, $errormsg, "E");  
				  continue;
			  }
			 
			  $toInsert = 0;
			  
			  //Check if Ledger already exist
			  $ledgerExistsSQL = "SELECT * FROM ledger WHERE ledger_name='$ledger_name' AND categoryid='$category_id'";
			  //echo "<BR>Query ". $ledgerExistsSQL;
			  $ledgerExists = $this->m_dbConn->select($ledgerExistsSQL);  
			  if($ledgerExists != '')
			  {
				  $ledgerID = $ledgerExists[0]['id']; 
				  $errorlog = "Ledger " . $ledger_name . " of categoryID <" .$category_id . "> found.";
				  if($this->debug_trace == 1)
				  {
					   echo "<BR>$errorlog ID:" . $ledgerID;
				  }
				  $this->obj_utility->logGenerator($errorfile,$rowCount,$errorlog,"W");
			  }
			  else
			  {	
				  if($this->debug_trace == 1)
				  {
					   echo "<BR>Ledger " . $ledger_name . " of categoryID <" .$category_id . "> not found ";
					//var_dump($ledgerExists);
					//continue;
				  }
				  if ($new_ledger_flag == 'on')
				  {
					  $toInsert = 1;
					  if($ledger_name != '')
					  {
						  if($sub_category_name != '')
						  {
							  $errorlog = "Creating new ledger with name: &lt;" . $ledger_name . "&gt; under sub-category : &lt;".$sub_category_name."&gt";
						  }
						  else
						  {
							  $errorlog = "Creating new ledger with name: &lt;" . $ledger_name . "&gt; under category : &lt;".$category_name."&gt";
						  }
						  if($this->debug_trace == 1)
						  {
							   echo "<BR>$errormsg";
						  }
						  //$this->obj_utility->logGenerator($errorfile,$rowCount,$errorlog,"I");
					  }
				  }
				  else
				  {
					  $errormsg = "Ledger &lt;".$ledger_name."&gt; doesn't exist. 'Create new ledger' not checked so new ledger not created.";						
					  $this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
					  if($this->debug_trace == 1)
					  {
						   echo "<BR>$errormsg";
						//var_dump($ledgerExists);
						//continue;
					  }
					  continue;
				  }
			  }
			  
			  //Insert the new ledger
  			  if($this->debug_trace == 1)
		  	  {
					echo "<BR>Group id = $group_id";
			  		echo "<BR>toInsert " . $toInsert;	
					echo "<BR>new_ledger_flag " . $new_ledger_flag;			  
			  }

			  if(strcasecmp($opening_type,"CREDIT")==0)
			  {
				  $account_type=1;
				  $transactionType = TRANSACTION_CREDIT;
			  }
			  else
			  {
				  $account_type=2;
				  $transactionType = TRANSACTION_DEBIT;
			  }
  
			  $opening_balance1 = 0;
			  if($import_opening_bal_ == 1)
			  {
			  	$opening_balance1 = $opening_balance;	
			  }

			  if ($toInsert == 1 && $new_ledger_flag == 'on') 
			  {
				  if($ledger_name <> 0 || $ledger_name <> '')
				  {
					  //echo "<BR>group_id : " . $group_id;
					  
					switch($group_id)
					{
						case 1://Liability:
						  $sale=0;
						  $purchase=1;
						  $income=0;
						  $expense=1;
						  $payment=1;
						  $receipt=0;
						break;
						case 2://Asset:
						  $sale=1;
						  $purchase=1;
						  $income=1;
						  $expense=0;
						  $payment=1;
						  $receipt=0;
						  if($category_id == $_SESSION['default_bank_account'] || $category_id == $_SESSION['default_cash_account'])
						  {
							  $payment = 1;
						  }
						break;
						case 3://Income:
						  $sale=1;
						  $purchase=0;
						  $income=1;
						  $expense=0;
						  $payment=0;
						  $receipt=1;
						break;
						case 4://Expense:
						  $sale=0;
						  $purchase=0;
						  $income=0;
						  $expense=1;
						  $payment=1;
						  $receipt=0;
						break;
						default:
						  echo "<BR>Unknown group $group_id<BR>";  
						  $errormsg= "Ledger Name: &lt;".$ledger_name."&gt; :: Unknown group: $groupname";
						  $this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"I");
						  continue;
						break;						
					}

					  $insert_ledger="insert into `ledger`(society_id,categoryid,show_in_bill,ledger_name,taxable,taxable_no_threshold,sale,purchase,income,expense,payment,receipt,opening_type,opening_balance,note,`opening_date`) values('$society_id','$category_id','$show_in_bill_flag','$ledger_name','$taxable_flag','$tax_no_threshold_flag','$sale','$purchase','$income','$expense','$payment','$receipt','$account_type','".abs($opening_balance)."','$note','".getDBFormatDate($Date)."')";

					  $NewLedgerID=$this->m_dbConn->insert($insert_ledger);
					  if($NewLedgerID > 0)
					  {
						  $m_TraceDebugInfo= "Created new Ledger. Name: &lt;".$ledger_name."&gt; :: New ID: &lt; $NewLedgerID &gt; ";
						  $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"W");
						  
						//set registers  
						$voucherID = 0;
						$voucherTypeID = 0;
						$isOpeningBalance = 1;
						
						$m_TraceDebugInfo = "Setting balance of ".$ledger_name." to &lt;" . $opening_balance ."&gt; for OpeningType &lt;$transactionType &gt;";
						$this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
						$insertAsset = 0;
						  if($group_id == LIABILITY)
						  {
							  $insertLiability = $this->obj_register->SetLiabilityRegister2(getDBFormatDate($Date), $group_id, $categoryID, $NewLedgerID, $voucherID, $voucherTypeID, $transactionType, abs($opening_balance1), $isOpeningBalance);
						  }
						  else if($group_id == ASSET)
						  {
								if($category_id == $_SESSION['default_bank_account'] || $category_id == $_SESSION['default_cash_account'])
								{
								  //Its a bank or cash ledger
								  $insertBank = $this->obj_register->SetBankRegister(getDBFormatDate($Date), $NewLedgerID, 0, 0, TRANSACTION_RECEIVED_AMOUNT, abs($opening_balance1), 0, 0, 1);
								  $insertBankMaster = $insert_query="insert into bank_master (`BankID`, `BankName`) values ('" . $NewLedgerID . "', '".$ledger_name."')";
								  $sqlInsertResult = $this->m_dbConn->insert($insertBankMaster);							
								}
								else
								{
								  $insertAsset = $this->obj_register->SetAssetRegister2(getDBFormatDate($Date), $group_id, $categoryID, $NewLedgerID, $voucherID, $voucherTypeID, $transactionType, abs($opening_balance1), $isOpeningBalance);

								}				
						  }
						  else if($group_id == INCOME)
						  {
							  $insertAsset = $this->obj_register->SetIncomeRegister($NewLedgerID, getDBFormatDate($Date), $voucherID, $voucherTypeID, $transactionType, abs($opening_balance1), $isOpeningBalance);
						  }				
						  else if($group_id == EXPENSE)
						  {
							  $insertAsset = $this->obj_register->SetExpenseRegister($NewLedgerID, getDBFormatDate($Date), $voucherID, $voucherTypeID, $transactionType, abs($opening_balance1), $isOpeningBalance);
						  }	
						  if($insertAsset > 0)
						  {
							$this->obj_utility->logGenerator($errorfile,$rowCount,"Amount " . $opening_balance1 . " updated.","W");  
						  }
						  else
						  {
							$this->obj_utility->logGenerator($errorfile,$rowCount,"Error updating Amount.","E");  
						  }

					  }
					  else
					  {	  $isImportSuccess = false;			  
						  $errormsg= "Error creating new Ledger : &lt;".$ledger_name."&gt;";
						  $this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");
					  }
				  }
				  else
				  {
					  $errormsg="Ledger name blank in Description Column";
					  $this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"W");
	  
				  }	
			  }
			  else
			  {
				  //Update existing Ledger
				  $ledger_sql = "";
				  $ledger_details_sql = "";
				  $m_TraceDebugInfo = "Updating ";
				  
				  //Remark given by the user for the ledger.
				  if (isset($Remark))
				  {
					  if($note != '') 
					  {
						  $ledger_sql .= "note='".$note."',";
  
						  $m_TraceDebugInfo .= "Remark to : &lt;" . $note . "&gt; ";
					  }
				  }
  
				  //Tariff Flag 
				  if (isset($TariffTag))
				  {
					  $ledger_sql .= "show_in_bill='".$show_in_bill_flag."',";

					  $m_TraceDebugInfo .= "Tariff Tag to : &lt;" . $show_in_bill_flag . "&gt; ";
				  }

				  //Tax Flag 
				  if (isset($TaxFlag))
				  {
					  $ledger_sql .= "taxable = $taxable_flag,";
	
					  $m_TraceDebugInfo .= "TaxFlag to : &lt;" . $taxable . "&gt; ";
				  }

				  //TaxNoThresholdFlag Flag 
				  if (isset($TaxNoThresholdFlag))
				  {
					  $ledger_sql .= "taxable_no_threshold = $tax_no_threshold_flag,";
	
					  $m_TraceDebugInfo .= "TaxNoThresholdFlag to : &lt;" . $tax_no_threshold . "&gt; ";
				  }
  
				  //GSTIN of the ledger
				  if (isset($GSTIN))
				  {
					  if ($gstin != '')
					  {
						  $ledger_details_sql = "GSTIN_No = '$gstin',";
  
						  $m_TraceDebugInfo .= "GSTIN to: &lt; ".$gstin . "&gt; ";
					  }
				  }
  
				  //PAN of the user or the ledger
				  if (isset($PAN))
				  {
					  if ($pan != '')
					  {
						  $ledger_details_sql = "PAN_No = '$pan',";
  
						  $m_TraceDebugInfo .= "PAN to: &lt;" . $pan . "&gt; ";
						  // $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
					  }
				  }
				  
				  //Opening Balance of the particular ledger & Opening Type - Credit or Debit of the ledger
				  if($import_opening_bal_ > 0)
				  {
					  if (isset($opening_balance1) )
					  {
						  if(isset($OpeningType))
						  {
					  
							  if (strcasecmp($opening_type,"DEBIT")==0)
							  {
								  $ledger_sql .= "opening_balance ='".$opening_balance."', opening_type= 2 ,";
								  $m_TraceDebugInfo .= "Updating balance to &lt;" . $opening_balance ."&gt; for OpeningType &lt;DEBIT &gt; ";
							  }
							  else
							  {
								  $ledger_sql .= "opening_balance ='".$opening_balance."', opening_type= 1 ,";
								  $m_TraceDebugInfo .= "Updating balance to &lt;" . $opening_balance ."&gt; for OpeningType &lt;CREDIT &gt; ";
							  }
						  }
						  else
						  {
							  $errormsg = "Opening type is invalid or not mentioned";
							  $this->obj_utility->logGenerator($errorfile,$rowCount,$errormsg,"E");				
						  }
					  }
				  }
				  else
				  {
					  $m_TraceDebugInfo .= " Opening Balance not updated ";
					  
				  }
				  
				  $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
				  
				  
				  $ledger_details_sql = rtrim($ledger_details_sql, ",");
				  $ledger_sql = rtrim($ledger_sql, ",");
    			  if($this->debug_trace == 1)
			  	  {
					  echo "<BR>ledger_details_sql : $ledger_details_sql";
					  echo "<BR>ledger_sql : $ledger_sql";
				  }
				  if ($ledger_details_sql != '')
				  {
					  $query = "UPDATE ledger_details SET " . $ledger_details_sql . " WHERE LedgerID = '$ledgerID'";
					  $returnUpdateVal = $this->m_dbConn->update($query);
					  if($returnUpdateVal > 0)
					  {
					   	$m_TraceDebugInfo = "Ledger Details Updated: &lt;" . $ledger_name . "&gt;";
					    $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"W");
					  }
					  else
					  {
					   	$m_TraceDebugInfo = "Ledger Details NOT Updated: &lt;" . $ledger_name . "&gt; Value may be same as before";
				  		$this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
					  }
				  }
				  else
				  {
					  $m_TraceDebugInfo = "  RESULT : No Ledger Details to Update: &lt;" . $ledger_name . "&gt;";
					  $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
				  }

				  //The values of remark, gstin & pan are appended in the query for updation
				  $m_TraceDebugInfo = "RESULT : ";
				  if ($ledger_sql != "")
				  {
					  $query = "UPDATE ledger SET ". $ledger_sql ." WHERE id = '$ledgerID'";
					  $returnUpdateVal = $this->m_dbConn->update($query);
					  if($returnUpdateVal > 0)
					  {
					   	$m_TraceDebugInfo .= "Ledger Updated: &lt;" . $ledger_name . "&gt;";
					    $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"W");
					  }
					  else
					  {
					   	$m_TraceDebugInfo .= "Ledger NOT Updated: &lt;" . $ledger_name . "&gt; There may not be changes";
						  $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
					  }
				  }
				  else
				  {
 			   	  		$m_TraceDebugInfo .= "No Ledger properties to Update: &lt;" . $ledger_name . "&gt;";
					  	$this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"I");
				  }
  
				  //If the group is ASSET, updating the `assetregister` table
				  if($import_opening_bal_ > 0)
				  {
					  if ($groupname === 'Asset')
					  {
						if($category_id == $_SESSION['default_bank_account'] || $category_id == $_SESSION['default_cash_account'])
						{
							  //echo "Bank or Cash Account";
							  //If the openingtype is DEBIT
							  if (strcasecmp($opening_type,"DEBIT")==0)
							  {
								  $updateRegister = $this->obj_register->UpdateBankRegister($Date, $ledgerID, TRANSACTION_RECEIVED_AMOUNT, $opening_balance1);								  
							  }
							  else
							  {
									$updateRegister = $this->obj_register->UpdateBankRegister($Date, $ledgerID, TRANSACTION_PAID_AMOUNT, $opening_balance1);  
							  }
						  }//If bank or cash
						  else
						  {
								$updateRegister = $this->obj_register->UpdateRegister_Ex($group_id, $category_id, $ledgerID, 0, $transactionType, $opening_balance1, $Date);
						  }
					  }//If Asset
					  else
					  {
						  $updateRegister = $this->obj_register->UpdateRegister_Ex($group_id, $category_id, $ledgerID, 0, $transactionType, $opening_balance1, $Date);
					  }//Else Asset
					  if($updateRegister <= 0)
					  {
						$this->obj_utility->logGenerator($errorfile,$rowCount,"Register not updated. Value may be same as before.","W");  
					  }
					  else
					  {
						$this->obj_utility->logGenerator($errorfile,$rowCount,"Amount " . $opening_balance1 . "  updated.","W");  
					  }
				  }
				  else  //if($import_opening_bal_ > 0)
				  {
				  } 
			  }//For existing Ledgers
		  }//loop
	  
		  if(!$isImportSuccess)
		  {
			  // echo 'Ledger Details not Updated';
			  $m_TraceDebugInfo = "Ledger Details not Imported!";	
			  $this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"E");	
		  }	
	  
		  $errormsg="[End Updating Ledgers]";
		  $this->obj_utility->logGenerator($errorfile,'End',$errormsg);
		  if($this->debug_trace == 1)
		  {
			  //die;	  
		  }
		}
		catch(Exception $exp)
		{
			$errormsg="Error occured in uploading document. Details are:".$exp->getMessage();
		  	$this->obj_utility->logGenerator($errorfile,$rowCount,$m_TraceDebugInfo,"E");	
		}
	  }
				  
					  
	  
	  public function combobox($query, $id, $defaultText)
	  {
		   echo "inside combobox..";
		  if($defaultText <> '')
		  {
			  $str = '<option value="0">' . $defaultText . '</option>';
		  }
		  
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
}
  
  ?>
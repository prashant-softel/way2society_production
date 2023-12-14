<?php 
if(!isset($_SESSION)){ session_start(); }
include_once("include/display_table.class.php");
include_once('dbconst.class.php');
include_once('utility.class.php');

?>
<?php
	
	class changeLog 
	{
		public $m_dbConn;
		public $m_dbConnRoot;
		
		function __construct($dbConn, $dbConnRoot = "")
		{
			$this->m_dbConn = $dbConn;
			$this->m_dbConnRoot = $dbConnRoot;
			$this->display_pg=new display_table($this->m_dbConn);
		}
		
		function setLog($desc, $changedBy, $changedTable, $changedKey, $Changed_Mode = "", $LastChangeId = 0)
		{
			$logID = 0;

			$currentTimeStamp = getCurrentTimeStamp(); 
			
			$sqlLog = "INSERT INTO `change_log`(`ChangedLogDec`, `ChangedBy`, `ChangedTable`, `ChangedKey`, `Changed_Mode`, `LastChangeId`, `ChangeTS`) VALUES ('" . $this->m_dbConn->escapeString($desc) . "', '" . $this->m_dbConn->escapeString($changedBy) . "', '" . $this->m_dbConn->escapeString($changedTable) . "', '" . $this->m_dbConn->escapeString($changedKey) . "', '" . $this->m_dbConn->escapeString($Changed_Mode) . "', '" . $this->m_dbConn->escapeString($LastChangeId) . "', '". $currentTimeStamp['DateTime']."')";
			
			$logID = $this->m_dbConn->insert($sqlLog);
			
			return $logID;
		}
		
		function getLog($logID)
		{
			$sqlLog = "Select log.ChangedLogDec, log.ChangeTS, log.ChangedTable, log.ChangedKey, user.name from change_log as log JOIN login as user on log.ChangedBy = user.login_id where log.ChangeLogID = '" . $logID . "'";
			
			$result = $this->m_dbConn->select($sqlLog);
			
			if($result <> "")
			{
				$response = array('success'=>'0', 'desc'=>$result[0]['ChangedLogDec'], 'user'=>$result[0]['name'], 'time'=>$result[0]['ChangeTS'], 'table'=>$result[0]['ChangedTable'], 'key'=>$result[0]['ChangedKey']);
				
				return $response;
			}
			else
			{
				$response = array('success'=>'1');
				
				return $response;
			}
			
			
		}
		
	
	public function display1($rsas, $bShowViewLink = false)
	{
		//echo "inside display1";
		$thheader=array('ChangeTS','ChangeBy','ChangedLogDec','ChangedTable');
		$this->display_pg->th=$thheader;
		$this->display_pg->mainpg="unit.php";
			//	echo "calling showunit";
		//$res=$this->display_pg->display_new($rsas);
		$res=$this->show_unit($rsas, $bShowViewLink);
		//echo "exiting display1";
		return $res;
	}
	
	public function getLogDetails($param){

		extract($param);

		// logModuleArr and StatusArr is set in dbconst.class.php file
		global $logModulesArr, $statusArr;
		
		$startDate = getDBFormatDate($startDate);

		$endDate = getDBFormatDate($endDate);

		$obj_utility = new utility($this->m_dbConn, $this->m_dbConnRoot);
		$loginDetails = $obj_utility->getSocietyAllLoginDetails();

		$link_log_qry = "SELECT GROUP_CONCAT(LastChangeId) as LinkedIds FROM change_log WHERE LastChangeId != 0 AND (DATE(ChangeTS) Between '$startDate' AND '$endDate')";

		$link_log_result = $this->m_dbConn->select($link_log_qry);

		$linkedLogIds = $link_log_result[0]['LinkedIds'];
		
		$logModulesStr = implode(',', array_keys($logModulesArr));

		$qry = "SELECT * FROM change_log WHERE ChangedTable IN($logModulesStr)";

		if(!empty($startDate) && !empty($endDate)){

			$qry .= " AND (DATE(ChangeTS) Between '$startDate' AND '$endDate')";
		}

		if(!empty($linkedLogIds)){

			$qry .= " AND ChangeLogID NOT IN($linkedLogIds) ";

		}

		if(!empty($login_id) && $login_id != 0){

			$qry .= " AND ChangedBy = '$login_id'";

		}

		if(!empty($module) && $module != 0){

			$qry .= " AND ChangedTable = '$module'";
		}

		$result = $this->m_dbConn->select($qry);

		if(!empty($result)){

			$table = "<table class='table table-bordered table-striped table-hover' id='example'>";
			$table .= "<thead><tr><th>View</th><th>Module</th><th>Status</th><th>Description</th><th>User</th><th>TimeStamp</th></tr></thead>";
			$table .= "<tbody>";
			
			
			foreach ($result as $key => $value) {
				
				$logDescArr = json_decode($value['ChangedLogDec'], true);
			
				$logDesc = $this->convertArrToString($logDescArr);
			
				$table .= "<tr>";
				$table .= "<td><a href='showLog.php?vTable=".$value['ChangedTable']."&refNo=".$value['ChangedKey']."' target='_blank'><img src='images/view.jpg' border='0' alt='View' style='cursor:pointer;' width='18' height='15'/></td>";
				$table .= "<td>".$logModulesArr[$value['ChangedTable']]."</td>"; 
				$table .= "<td>".$statusArr[$value['Changed_Mode']]."</td>";
				$table .= "<td>".$logDesc."</td>";
				$table .= "<td>".$loginDetails[$value['ChangedBy']]."</td>";
				$table .= "<td>".$value['ChangeTS']."</td>";
				$table .= "</tr>"; 

			}
			
			$table .= "</tbody>";
			$table .= "</table>";

			return $table;

		}

	}


	public function pgnation($bShowViewLink = false)
	{
		//$_REQUEST['ChangedBy']  $_REQUEST['ChangeTSFrom']  $_REQUEST['ChangeTSTo']
		if($_REQUEST['method']=='applyFilter')
		{
			
			$sql1 = "SELECT chnglogtbl.ChangeLogID,chnglogtbl.ChangeTS,logintbl.name,chnglogtbl.ChangedLogDec,chnglogtbl.ChangedTable,chnglogtbl.ChangedKey FROM `change_log` as chnglogtbl JOIN `login` as logintbl on chnglogtbl.ChangedBy=logintbl.login_id WHERE 1";
			if($_REQUEST['ChangedBy'] > 0)
			{
				$sql1 .="  and chnglogtbl.ChangedBy='".$_REQUEST['ChangedBy']."'";
			}
			
			if($_REQUEST['ChangeTSFrom'] > 0 && $_REQUEST['ChangeTSTo'] > 0)
			{
				$sql1 .="  and chnglogtbl.ChangeTS between '".$_REQUEST['ChangeTSFrom']."' and '".$_REQUEST['ChangeTSTo']."'";
			}
			
			if($_REQUEST['ChangeTableName'] <> "" && $_REQUEST['ChangeTableName'] <> "Please Select")
			{
				$sql1 .="  and chnglogtbl.ChangedTable='".$_REQUEST['ChangeTableName']."'";
			}
		}
		else
		{
			$sql1 = "SELECT chnglogtbl.ChangeLogID,chnglogtbl.ChangeTS,logintbl.name,chnglogtbl.ChangedLogDec,chnglogtbl.ChangedTable,chnglogtbl.ChangedKey FROM `change_log` as chnglogtbl JOIN `login` as logintbl on chnglogtbl.ChangedBy=logintbl.login_id where 1";
		}
		//echo $sql1;
		if($_REQUEST['method']=='applyFilter')
		{
			$cntr = "SELECT chnglogtbl.ChangeLogID,chnglogtbl.ChangeTS,logintbl.name,chnglogtbl.ChangedLogDec,chnglogtbl.ChangedTable,chnglogtbl.ChangedKey FROM `change_log` as chnglogtbl JOIN `login` as logintbl on chnglogtbl.ChangedBy=logintbl.login_id where 1";
			
			if($_REQUEST['ChangedBy'] > 0)
			{
				$cntr .="  and chnglogtbl.ChangedBy='".$_REQUEST['ChangedBy']."'";
			}
			
			if($_REQUEST['ChangeTSFrom'] > 0 && $_REQUEST['ChangeTSTo'] > 0)
			{
				$cntr .="  and chnglogtbl.ChangeTS between '".$_REQUEST['ChangeTSFrom']."' and '".$_REQUEST['ChangeTSTo']."'";
			}
			
			if($_REQUEST['ChangeTableName'] <> "" && $_REQUEST['ChangeTableName'] <> "Please Select")
			{
				$cntr .="  and chnglogtbl.ChangedTable='".$_REQUEST['ChangeTableName']."'";
			}
		}
		else
		{
			$cntr = "SELECT chnglogtbl.ChangeLogID,chnglogtbl.ChangeTS,logintbl.name,chnglogtbl.ChangedLogDec,chnglogtbl.ChangedTable,chnglogtbl.ChangedKey FROM `change_log` as chnglogtbl JOIN `login` as logintbl on chnglogtbl.ChangedBy=logintbl.login_id where 1";
		}
		//$thheader=array('ChangeTS','ChangeBy','ChangedLogDec','ChangedTable');
		$thheader = array('TimeStamp','Change By','Changed Log Decription','Changed Table','Changed Key');	
		//$thheader = array('ChangeTS','ChangeBy','ChangedLogDec','ChangedTable','ChangedKey');	
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "ChangeLog.php";
		$this->display_pg->sql1=$sql1;
		$this->display_pg->cntr1=$cntr;
		$limit = "2000";
		$page=$_REQUEST['page'];
		//echo $sql1;
		$result=$this->m_dbConn->select($sql1);
		//print_r($result);
		$res = $this->display_pg->display_datatable($result,false,false);
		return $res;
		
		//$result = $this->m_dbConn->select($sql1);
		//$this->show_unit($result, false);
	}
	/*
	public function show_unit($res, $bShowViewLink = false)
	{
		
		if($res<>"")
		{
					
			//print_r($res);
			if(!isset($_REQUEST['page']))
			{
				$_REQUEST['page'] = 1;
			}
			$iCounter = 0;
			$sortOrder=0;
	
		?>
		<table id="example" style="text-align:center; width:100%;" class="table table-bordered table-hover table-striped">
        <thead>
		<tr height="30">
       		<th width="150" style="text-align:center">Change TimeStamp</th>
            <th width="150" style="text-align:center">Changed By</th>
            <th width="250" style="text-align:center">Changed Log Description</th>
            <th width="100" style="text-align:center">Changed Table</th>
         </tr>
        </thead>
        <tbody>
		<?php 
		//print_r($res);
		foreach($res as $k => $v){
			$iCounter++;
			?>
        	<td align="center"><?php echo $res[$k]['ChangeTS'];?></td>
            <td align="center"><?php echo $res[$k]['name'];?></td>
            <td align="center"><?php echo $res[$k]['ChangedLogDec'];?></td>
            <td align="center"><?php echo $res[$k]['ChangedTable'];?></td>
            
     </tr>
        <?php 
		}?>
        
        </tbody>
        </table>

		<?php
		}
		else
		{
			?>
            <table align="center" border="0">
            <tr>
            	<td><font color="#FF0000" size="2"><b>No Records Found.</b></font></td>
            </tr>
            </table>
            <?php		
		}
	}*/
	
	public function getlogChanges($RequestedID, $tableName)
	{	
		
		if(!empty($tableName) && !empty($RequestedID)){

			$Sql = "SELECT * from change_log where ChangedKey = '".$RequestedID."' and ChangedTable = '".$tableName."' ORDER BY `ChangeTS` DESC";
			return $result = $this->m_dbConn->select($Sql);	
		
		}
		
	}
	
	public function comboboxEx($query)
	{
		$id=0;
		//echo "<script>alert('test')<//script>";
		$str.="<option value=''>Please Select</option>";
	$data = $this->m_dbConn->select($query);
	//echo "<script>alert('test2')<//script>";
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
					//echo "<script>alert('".$str."')<//script>";
					$i++;
				}
			}
		}
		//return $str;
		//print_r( $str);
		//echo "<script>alert('test')<//script>";
		return $str;
	}


	public function showChangeLog($vTable, $refNo, $getLastRowOnly = false){


		try {
			
			$data = array();
			
			$query = "SELECT * FROM `change_log` where ChangedTable = '".$vTable."' AND ChangedKey = '$refNo' ORDER BY ChangeLogID DESC LIMIT 1";	
			$result = $this->m_dbConn->select($query);

			if($getLastRowOnly){

				return $result;
			}
			
			array_push($data, $result[0]);
			$previousLinkID = $result[0]['LastChangeId'];	
			
			if($previousLinkID <> 0){
				
				do {
					$query = "SELECT * FROM `change_log` where ChangeLogID = '$previousLinkID'";
					$result = $this->m_dbConn->select($query);
					array_push($data, $result[0]);
					$previousLinkID = $result[0]['LastChangeId'];
				} while ($previousLinkID <> 0);
			}

			return array_reverse($data);
			
		} catch (Exception $e) {
			
		}
	}
	
	public function convertArrToString($data){

		$desc = '';
		foreach ($data as $name => $value) {
			
			if(gettype($value) == 'array'){
				
				$desc .= "<b>".$name."</b>: ".$this->convertArrToString($value).", "; 
			}
			else{
				$desc .= "<b>".$name."</b>: ".$value.", "; 
			}
			
		}
		return rtrim($desc, ', ');

	}

	public function getTableName($refTable){

		$table = '';

		if($refTable == TABLE_BILLREGISTER){ // data will get only if something edited in bill

			$table = 'billdetails';
		}
		else if($refTable == TABLE_CHEQUE_DETAILS){ //we can show data for receipt entry
			
			$table = 'chequeentrydetails';
		}
		else if($refTable == TABLE_PAYMENT_DETAILS){ // we can show data for receipt entry
			
			$table = 'paymentdetails';
		}
		if($refTable == TABLE_CREDIT_DEBIT_NOTE){
			
			$table = 'credit_debit_note';
		}
		else if($refTable == TABLE_SALESINVOICE){
			
			$table = 'sale_invoice';
		}
		return $table;
	}

	public function checkLogExitsInOlderFormat($refTable, $refNo){

		try {
			
			$tableName = $this->getTableName($refTable);

			if(empty($tableName)){ 

				return false;
			}

			// First check in log table
			$query = "SELECT ChangedLogDec, ChangeTS, ChangedBy  FROM `change_log` WHERE LOWER(`ChangedTable`) = '$tableName' AND ChangedKey = '$refNo'";

			$result = $this->m_dbConn->select($query);

			// if record not found then check in root tables
			
			if(empty($result) && $refTable != TABLE_BILLREGISTER){ // Bill details does not store any user and timestamp information

				$column = 'ID';
				if($refTable == TABLE_PAYMENT_DETAILS){

					$column = 'id';
				}
				
				$rootTableQry = "SELECT * FROM $tableName WHERE $column = '$refNo'";
				$rootTableDetail = $this->m_dbConn->select($rootTableQry);

				if($refTable == TABLE_SALESINVOICE || $refTable == TABLE_CREDIT_DEBIT_NOTE){

					$loginID = ($rootTableDetail[0]['LatestChangeID'] == 0)?$rootTableDetail[0]['CreatedBy_LoginID']:$rootTableDetail[0]['LatestChangeID'];
					$Timestamp = ($rootTableDetail[0]['LatestChangeID'] == 0)?$rootTableDetail[0]['CreatedTimestamp']:$rootTableDetail[0]['LastModified'];
					$status = ($rootTableDetail[0]['LatestChangeID'] == 0)?ADD:EDIT;
				}
				else if($refTable == TABLE_CHEQUE_DETAILS || $refTable == TABLE_PAYMENT_DETAILS)
				{
					$loginID = $rootTableDetail[0]['EnteredBy'];
					$Timestamp = $rootTableDetail[0]['Timestamp'];
					$status = ADD;
				}

				return array(array('ChangedTable'=>$refTable, 'Changed_Mode'=>$status, 'ChangedLogDec'=> json_encode(array('Details' => 'Please view the Ledger Detail for more detail')), 'ChangedBy'=> $loginID, 'ChangeTS'=>$Timestamp));
			}
			else{

				foreach ($result as $key => $row) {
				
					$result[$key]['ChangedTable'] = $refTable;
					$result[$key]['Changed_Mode'] = NONE;
					$result[$key]['ChangedLogDec'] = json_encode(array('Details' => $row['ChangedLogDec']));
	
				}
			}
			return $result;

		} catch (Exception $e) {
			
			return $e->getMessage();

		}


	}

	public function showFullLogDetail($refTable, $refNo){

		$obj_utility = new utility($this->m_dbConn, $this->m_dbConnRoot);

		$data = $this->showChangeLog($refTable, $refNo);
		
		global $logModulesArr;

		if(empty($data[0])){

			// Data is not exits in current format then search in older format
			$data = $this->checkLogExitsInOlderFormat($refTable, $refNo);
		}

		$statusArr = array(ADD=>"Added", EDIT=>"Edited", DELETE=>"Deleted", NONE=>"--");

		$loginDetails = $obj_utility->getSocietyAllLoginDetails();

		$logHeaders = array_keys(json_decode($data[0]['ChangedLogDec'], true));

		$cnt = 0;
		$tempArr = array();
		if(!empty($data[0])){
	
		$table = "<table class='table table-bordered table-striped table-hover' id='example'>
					<thead><tr>
						<th class='row-th'>Module</th>";
					
					foreach ($logHeaders as $head) { 
						
						$table .= "<th class='row-th'>".ucfirst($head)."</th>";
					}

					$table .= "<th class='row-th'>Status</th>
					<th class='row-th'>Edited By</th>
					<th class='row-th'>TimeStamp</th>
					</tr>
					</thead> 
					<tbody>";  
					
					foreach ($data as $detail) { 
						
						$rowData = json_decode($detail['ChangedLogDec'], true);
						
						$table .= "	<tr>
							<td class='row-td'>".$logModulesArr[$detail['ChangedTable']]."</td>";
						
						foreach ($logHeaders as $indexKey) {
							
							if(gettype($rowData[$indexKey]) == 'array'){
								
								$rowData[$indexKey] = $this->convertArrToString($rowData[$indexKey]);
							}
							
							if($cnt != 0){

								if($rowData[$indexKey] != $tempArr[$indexKey]){ 

								$table .= "<td class='row-td diffColor' ><b>".$rowData[$indexKey]."</b></td>";
								}
								else{

									$table .= "<td class='row-td'>".$rowData[$indexKey]."</td>";
								}
							}
							else{ 

								$table .= "<td class='row-td'>".$rowData[$indexKey]."</td>";
							}
							$tempArr[$indexKey] = $rowData[$indexKey];
						}
						
						$table .= "<td class='row-td'>".$statusArr[$detail['Changed_Mode']]."</td><td class='row-td'>".$loginDetails[$detail['ChangedBy']]."</td><td class='row-td'>".$detail['ChangeTS']."</td>
						</tr>";
						$cnt++;
					}
					
					$table .= "</tbody>
			</table>";
			}
			else{

				$table = "<table class='table table-bordered table-striped table-hover' id='example'>
						  <thead><tr><th>Module</th><th>Status</th><th>Edited By</th><th>TimeStamp</th></tr></thead>
						  </table>";
			}
		
	return $table;				



	}
	
	}
?>
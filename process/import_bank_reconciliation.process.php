<?php 
  include_once("../classes/import_bank_statement.class.php");
  require_once("../classes/CsvOperations.class.php");
  $obj_import_bank_reco = new Import_Bank_Statement();
  
  $FileData = $_SESSION['file_data'];
  $ImportData = array();
  
 //var_dump($FileData);
  
  $Cnt = 0;
  $bankID = $_POST['bankid']; 
  
  for($i = 2 ; $i < count($FileData) -1 ; $i++)
  {
	$ImportData[$Cnt]['BankID'] = $bankID;
	$ImportData[$Cnt]['Date'] = $FileData[$i][0];
	$ImportData[$Cnt]['ChequeNumber'] = $FileData[$i][1];
	$ImportData[$Cnt]['Bank_Description'] = $FileData[$i][2];
	$ImportData[$Cnt]['Debit'] = $FileData[$i][3];
	$ImportData[$Cnt]['Credit'] = $FileData[$i][4];
	$ImportData[$Cnt]['Bank_Balance'] = $FileData[$i][5];
	$ImportData[$Cnt]['Notes'] = $FileData[$i][6];
	$Cnt++;
 }
  	  
  $validator = $obj_import_bank_reco->ImportActualBankStmt($ImportData);
  
 
  $ErrorLog = $obj_import_bank_reco->errorLog;
?>
<html>
<body>
<form name="Goback" method="post" action="../import_bank_statement.php?LedgerID=<?php echo $bankID;?>">

<input type="hidden" name="ShowData" value="<?php echo $ShowData; ?>">

</form>
<script>	
	//window.location.href = "../bank_reconciliation.php?LedgerID=347"; 
	window.open("<?php echo $ErrorLog ?>");
	document.Goback.submit();
</script>
</body>
</html>

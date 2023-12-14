<?php include_once("../classes/include/dbop.class.php");
		include_once("../classes/bank_reconciliation.class.php");
	  
	  $dbConn = new dbop();
	  $obj_bank_reco = new bank_reconciliation($dbConn);
	  //echo "reqprocess:".$_POST["ledgerID"];	  
	  $validator = $obj_bank_reco->startProcess();
	  //$validator = "Insert";	  
?>
<html>
<body>
<form name="Goback" method="post" action="../bank_reconciliation.php?ledgerID=<?php echo $_REQUEST["ledgerID"]; ?>" >

	<?php

	if($validator=="Insert")
	{		
		$ShowData="Record Added Successfully";		
	}	
	else
	{		
		foreach($_POST as $key=>$value)
		{
			if($key == "ledgerID")
			{			
				echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />";
			}
		}
		$ShowData=$validator;
	}
	?>

<input type="hidden" name="ShowData" value="<?php echo $ShowData; ?>">
<input type="hidden" name="status" value = '<?php echo $_POST['status'] ?>'  />
<input type="hidden" name="voucherType" value = '<?php echo $_POST['voucherType'] ?>'  />
<input type="hidden" name="dateType"  value = '<?php echo $_POST['dateType']?>'  />
<input type="hidden" name="chequeNo"  value = '<?php echo $_POST['chequeNo']?>'  />
<input type="hidden" name="From" value = '<?php echo $_POST['From']?>'  />
<input type="hidden" name="To" value = '<?php echo $_POST['To']?>'  />
</form>
<script>	
	//window.location.href = "../bank_reconciliation.php?LedgerID=347"; 
	document.Goback.submit();
</script>
</body>
</html>
